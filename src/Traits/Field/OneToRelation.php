<?php
/**
 * Add multiple methods for many to many relations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Illuminate\Support\Collection;
use Laramore\Elements\OperatorElement;
use Laramore\Facades\Operator;
use Laramore\Fields\{
    BaseLink, Constraint\FieldConstraintHandler
};
use Laramore\Contracts\Eloquent\{
    LaramoreModel, LaramoreBuilder,
};

trait OneToRelation
{
    use ModelRelation, Constraints;

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
     * Reversed name of this relation.
     *
     * @var string
     */
    protected $reversedName;

    /**
     * Name for this relation.
     *
     * @var string
     */
    protected $relationName;

    /**
     * Return the reversed field.
     *
     * @return BaseLink
     */
    public function getReversed(): BaseLink
    {
        return $this->getField('reversed');
    }

    /**
     * Return the relation handler for this meta.
     *
     * @return FieldConstraintHandler
     */
    public function getConstraintHandler(): FieldConstraintHandler
    {
        return $this->getField('id')->getConstraintHandler();
    }

    /**
     * Define the attribute name.
     *
     * @param string $name
     * @return self
     */
    public function to(string $name)
    {
        $this->needsToBeUnowned();

        $this->defineProperty('to', $this->getReversed()->from = $name);

        return $this;
    }

    /**
     * Define the model on which to point.
     *
     * @param string $model
     * @param string $reversedName
     * @param string $relationName
     * @return self
     */
    public function on(string $model, string $reversedName=null, string $relationName=null)
    {
        $this->needsToBeUnowned();

        if ($model === 'self') {
            $this->defineProperty('on', $model);
        } else {
            $this->defineProperty('on', $this->getReversed()->off = $model);
            $this->to($this->getReversed()->off::getMeta()->getPrimary()->getAttribute()->attname);
        }

        if ($reversedName) {
            $this->setProperty('reversedName', $reversedName);
        } else if ($model === 'self') {
            $this->reversedName($this->getConfig('self_reversed'));
        }

        if ($relationName) {
            $this->setProperty('relationName', $relationName);
        }

        return $this;
    }

    /**
     * Define on self.
     *
     * @return self
     */
    public function onSelf()
    {
        return $this->on('self');
    }

    /**
     * Define the reversed name of the relation.
     *
     * @param string $reversedName
     * @return self
     */
    public function reversedName(string $reversedName=null)
    {
        $this->needsToBeUnowned();
        $this->needsToBeUnlocked();

        $this->fieldsName['reversed'] = $reversedName;

        return $this;
    }

    /**
     * Define on, off and from variables after being owned.
     *
     * @return void
     */
    protected function owned()
    {
        if ($this->on === 'self') {
            $this->defineProperty('on', $this->getReversed()->off = $this->getMeta()->getModelClass());
            $this->defineProperty('to', $this->getReversed()->off::getMeta()->getPrimary()->getAttribute()->attname);
        }

        parent::owned();

        $this->defineProperty('off', $this->getReversed()->on = $this->getMeta()->getModelClass());
        $this->defineProperty('from', $this->getReversed()->to = $this->getField('id')->attname);

        $relationName = $this->hasProperty('relationName') ? $this->getProperty('relationName') : null;
        $this->foreign($this->on::getMeta()->getField($this->to), $relationName);
    }

    /**
     * Check all options.
     *
     * @return void
     */
    protected function checkOptions()
    {
        if (!$this->on) {
            throw new \Exception('Related model settings needed. Set it by calling `on` method');
        }

        $this->defineProperty('reversedName', $this->getReversed()->name);

        parent::checkOptions();
    }

    /**
     * Indicate if it is a relation on itself.
     *
     * @return boolean
     */
    public function isOnSelf()
    {
        return \in_array($this->on, [$this->getMeta()->getModelClass(), 'self']);
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

        return isset($value[$this->to]) ? $value[$this->to] : $value;
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
        if (\is_null($value) || $value instanceof $this->on || \is_array($value) || $value instanceof Collection) {
            return $value;
        }

        $model = new $this->on;
        $model->setAttributeValue($this->to, $value);

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
     * Add a where null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @param  boolean         $not
     * @return LaramoreBuilder
     */
    public function whereNull(LaramoreBuilder $builder, $value=null, string $boolean='and', bool $not=false): LaramoreBuilder
    {
        return $this->getField('id')->addBuilderOperation($builder, 'whereNull', $boolean, $not);
    }

    /**
     * Add a where not null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @return LaramoreBuilder
     */
    public function whereNotNull(LaramoreBuilder $builder, $value=null, string $boolean='and'): LaramoreBuilder
    {
        return $this->whereNull($builder, $value, $boolean, true);
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
        return $this->getField('id')->addBuilderOperation($builder, 'whereIn', $value, $boolean, $notIn);
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
        if ($operator->needs === 'collection') {
            return $this->whereIn($builder, $value, $boolean, ($operator === Operator::notIn()));
        }

        return $this->getField('id')->addBuilderOperation($builder, 'where', $operator, $value, $boolean);
    }
}