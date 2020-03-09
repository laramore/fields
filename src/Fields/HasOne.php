<?php
/**
 * Define a reverse one to one field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Collection;
use Laramore\Facades\Operator;
use Laramore\Elements\OperatorElement;
use Laramore\Fields\BaseComposed;
use Laramore\Contracts\{
    Proxied, Eloquent\LaramoreModel, Eloquent\LaramoreBuilder
};
use Laramore\Contracts\Field\Constraint\{
    SourceConstraint, TargetConstraint
};

class HasOne extends BaseLink
{
    /**
     * LaramoreModel from the relation is.
     *
     * @var LaramoreModel
     */
    protected $off;

    /**
     * AttributeField name from the relation is.
     *
     * @var string
     */
    protected $from;

    /**
     * LaramoreModel from the relation is.
     *
     * @var LaramoreModel
     */
    protected $on;

    /**
     * AttributeField name from the relation is.
     *
     * @var string
     */
    protected $to;

    /**
     * Return the reversed field.
     *
     * @return BaseComposed
     */
    public function getReversed(): BaseComposed
    {
        return $this->getOwner();
    }

    /**
     * Return the source of the relation.
     *
     * @return SourceConstraint
     */
    public function getSource(): SourceConstraint
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getSource();
    }

    /**
     * Return the target of the relation.
     *
     * @return TargetConstraint
     */
    public function getTarget(): TargetConstraint
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getTarget();
    }

    /**
     * Dry the value in a simple format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function dry($value)
    {
        return $this->transform($value)->map(function ($value) {
            return $value[$this->from];
        });
    }

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        return $this->transform($value);
    }

    /**
     * Transform the value to be used as a correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        if (\is_null($value) || ($value instanceof $this->on)) {
            return $value;
        }

        $model = new $this->on;
        $model->setAttributeValue($model->getKeyName(), $value);

        return $model;
    }

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $value;
    }

    /**
     * Add a where null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @param  boolean         $not
     * @param  \Closure        $callback
     * @return LaramoreBuilder
     */
    public function whereNull(LaramoreBuilder $builder, $value=null, string $boolean='and',
                              bool $not=false, \Closure $callback=null): LaramoreBuilder
    {
        if ($not) {
            return $this->whereNotNull($builder, $value, $boolean, null, null, $callback);
        }

        return $builder->doesntHave($this->name, $boolean, $callback);
    }

    /**
     * Add a where not null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @param  mixed           $operator
     * @param  integer         $count
     * @param  \Closure        $callback
     * @return LaramoreBuilder
     */
    public function whereNotNull(LaramoreBuilder $builder, $value=null, string $boolean='and',
                                 $operator=null, int $count=1, \Closure $callback=null): LaramoreBuilder
    {
        return $builder->has($this->name, (string) ($operator ?? Operator::supOrEq()), $count, $boolean, $callback);
    }

    /**
     * Add a where in condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  Collection      $value
     * @param  string          $boolean
     * @param  boolean         $notIn
     * @return LaramoreBuilder
     */
    public function whereIn(LaramoreBuilder $builder, Collection $value=null,
                            string $boolean='and', bool $notIn=false): LaramoreBuilder
    {
        return $this->on::getMeta()->getPrimary()->addBuilderOperation($builder, 'whereIn', $value, $boolean, $notIn);
    }

    /**
     * Add a where not in condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  Collection      $value
     * @param  string          $boolean
     * @return LaramoreBuilder
     */
    public function whereNotIn(LaramoreBuilder $builder, Collection $value=null, string $boolean='and'): LaramoreBuilder
    {
        return $this->whereIn($builder, $value, $boolean, true);
    }

    /**
     * Add a where condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  OperatorElement $operator
     * @param  mixed           $value
     * @param  string          $boolean
     * @return LaramoreBuilder
     */
    public function where(LaramoreBuilder $builder, OperatorElement $operator,
                          $value=null, string $boolean='and'): LaramoreBuilder
    {
        return $this->on::getMeta()->getPrimary()->addBuilderOperation($builder, 'where', $operator, $value, $boolean);
    }

    /**
     * Retrieve values from the relation field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function retrieve(LaramoreModel $model)
    {
        return $this->relate($model)->getResults();
    }

    /**
     * Use the relation to set the other field values.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function consume(LaramoreModel $model, $value)
    {
        $value->setAttribute($this->getReversed()->name, $model);

        return $value;
    }

    /**
     * Return the query with this field as condition.
     *
     * @param  Proxied $model
     * @return LaramoreBuilder
     */
    public function relate(Proxied $model)
    {
        return $model->hasOne($this->on, $this->to, $this->from);
    }

    /**
     * Reverbate the relation into database.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return boolean
     */
    public function reverbate(LaramoreModel $model, $value): bool
    {
        $primary = $this->on::getMeta()->getPrimary();
        $id = $model->getKey();
        $valueId = $value[$primary->getNative()];

        $primary->addBuilderOperation(
            $this->on::where($this->to, $id),
            'where',
            $valueId
        )->update([$this->to => null]);

        $primary->addBuilderOperation(
            (new $this->on)->newQuery(),
            'where',
            $valueId
        )->update([$this->to => $id]);

        return true;
    }
}
