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
use Laramore\Contracts\{
    Eloquent\LaramoreModel, Eloquent\LaramoreBuilder
};
use Laramore\Contracts\Field\{
    AttributeField, RelationField, Constraint\SourceConstraint, Constraint\TargetConstraint
};
use Laramore\Traits\Field\ModelRelation;

class HasOne extends BaseField implements RelationField
{
    use ModelRelation;

    /**
     * Return the reversed field.
     *
     * @return RelationField
     */
    public function getReversed(): RelationField
    {
        return $this->getOwner();
    }

    /**
     * Indicate if the relation is head on or not.
     * Usefull to know which to use between source and target.
     *
     * @return boolean
     */
    public function isRelationHeadOn(): bool
    {
        return false;
    }

    /**
     * Model where the relation is set from.
     *
     * @return string
     */
    public function getSourceModel(): string
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getSourceModel();
    }

    /**
     * Return all attributes where to start the relation from.
     *
     * @return array<AttributeField>
     */
    public function getSourceAttributes(): array
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getSourceAttributes();
    }

    /**
     * Return the main attribute where to start the relation from.
     *
     * @return AttributeField
     */
    public function getSourceAttribute(): AttributeField
    {
        return $this->getSourceAttributes()[0];
    }

    /**
     * Model where the relation is set to.
     *
     * @return string
     */
    public function getTargetModel(): string
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getTargetModel();
    }

    /**
     * Return all attributes where to start the relation to.
     *
     * @return array<AttributeField>
     */
    public function getTargetAttributes(): array
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getTargetAttributes();
    }

    /**
     * Return the main attribute where to start the relation to.
     *
     * @return AttributeField
     */
    public function getTargetAttribute(): AttributeField
    {
        return $this->getTargetAttributes()[0];
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
        $value = $this->transform($value);
        $name = $this->getSourceAttribute()->getNative();

        return $this->transform($value)->map(function ($value) use ($name) {
            return isset($value[$name]) ? $value[$name] : $value;
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
        $modelClass = $this->getSourceModel();

        if (\is_null($value) || ($value instanceof $modelClass)) {
            return $value;
        }

        $model = new $modelClass;
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
        return $this->getSourceModel()::getMeta()->getPrimary()->getAttribute()
            ->addBuilderOperation($builder, 'whereIn', $value, $boolean, $notIn);
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
        return $this->getSourceModel()::getMeta()->getPrimary()->getAttribute()
            ->addBuilderOperation($builder, 'where', $operator, $value, $boolean);
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
     * Return the relation with this field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function relate(LaramoreModel $model)
    {
        return $model->hasOne(
            $this->getSourceModel(),
            $this->getTargetAttribute()->getNative(),
            $this->getSourceAttribute()->getNative()
        );
    }

    /**
     * Reverbate the relation into database or other fields.
     * It should be called by the set method.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function reverbate(LaramoreModel $model, $value)
    {
        if (!\is_null($value)) {
            $this->getField('id')->set($model, $this->getTarget()->getModelValue($value));
        }

        $modelClass = $this->getSourceModel();
        $modelClass = $this->getSourceModel();
        $primary = $modelClass::getMeta()->getPrimary()->getAttribute();
        $id = $model->getKey();
        $valueId = $value[$primary->getNative()];

        $primary->addBuilderOperation(
            $modelClass::where($this->to, $id),
            'where',
            $valueId
        )->update([$this->to => null]);

        $primary->addBuilderOperation(
            (new $modelClass)->newQuery(),
            'where',
            $valueId
        )->update([$this->to => $id]);

        return $value;
    }
}
