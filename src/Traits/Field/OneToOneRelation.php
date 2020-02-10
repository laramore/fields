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
use Laramore\Eloquent\Builder;
use Laramore\Facades\Operator;
use Laramore\Fields\LinkField;
use Laramore\Interfaces\IsALaramoreModel;

trait OneToOneRelation
{
    /**
     * Model from the relation is.
     *
     * @var IsALaramoreModel
     */
    protected $off;

    /**
     * Attribute name from the relation is.
     *
     * @var string
     */
    protected $from;

    /**
     * Model from the relation is.
     *
     * @var IsALaramoreModel
     */
    protected $on;

    /**
     * Attribute name from the relation is.
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
     * @return LinkField
     */
    public function getReversed(): LinkField
    {
        return $this->getLink('reversed');
    }

    /**
     * Define the attribute name.
     *
     * @param string $name
     * @return self
     */
    public function to(string $name)
    {
        $this->needsToBeUnlocked();

        if ($this->hasProperty('to')) {
            throw new \Exception('The property to cannot be reset. Set it before assigning an ');
        }

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
        $this->needsToBeUnlocked();

        if ($model === 'self') {
            $this->defineProperty('on', $model);
        } else {
            $this->defineProperty('on', $this->getReversed()->off = $model);
            $this->to($this->getReversed()->off::getMeta()->getPrimary()->getAttribute()->attname);
        }

        if ($reversedName) {
            $this->setProperty('reversedName', $reversedName);
        } else if ($model === 'self') {
            $this->reversedName($this->getConfig('self_reversed_name_template'));
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

        $this->linksName['reversed'] = $reversedName;

        return $this;
    }

    /**
     * Define on, off and from variables after being owned.
     *
     * @return void
     */
    public function owned()
    {
        if ($this->on === 'self') {
            $this->on($this->getMeta()->getModelClass());
        }

        parent::owned();

        $this->defineProperty('off', $this->getReversed()->on = $this->getMeta()->getModelClass());
        $this->defineProperty('from', $this->getReversed()->to = $this->getAttribute('id')->attname);
    }

    /**
     * Define all constraints for this field.
     *
     * @return void
     */
    protected function setConstraints()
    {
        parent::setConstraints();

        $relationName = $this->hasProperty('relationName') ? $this->getProperty('relationName') : null;

        $this->foreign('id', $this->on::getMeta()->getAttribute($this->to), $relationName);
    }

    /**
     * Check all rules.
     *
     * @return void
     */
    protected function checkRules()
    {
        if (!$this->on) {
            throw new \Exception('Related model settings needed. Set it by calling `on` method');
        }

        $this->defineProperty('reversedName', $this->getReversed()->name);

        parent::checkRules();
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
        $model->setRawAttribute($this->to, $value);

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
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function retrieve(IsALaramoreModel $model)
    {
        return $this->relate($model)->getResults();
    }

    /**
     * Add a where null condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @param  boolean $not
     * @return Builder
     */
    public function whereNull(Builder $builder, $value=null, string $boolean='and', bool $not=false): Builder
    {
        return $this->getAttribute('id')->addBuilderOperation($builder, 'whereNull', $boolean, $not);
    }

    /**
     * Add a where not null condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @return Builder
     */
    public function whereNotNull(Builder $builder, $value=null, string $boolean='and'): Builder
    {
        return $this->whereNull($builder, $value, $boolean, true);
    }

    /**
     * Add a where in condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @param  boolean    $notIn
     * @return Builder
     */
    public function whereIn(Builder $builder, Collection $value=null, string $boolean='and', bool $notIn=false): Builder
    {
        return $this->getAttribute('id')->addBuilderOperation($builder, 'whereIn', $value, $boolean, $notIn);
    }

    /**
     * Add a where not in condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @return Builder
     */
    public function whereNotIn(Builder $builder, Collection $value=null, string $boolean='and'): Builder
    {
        return $this->whereIn($builder, $value, $boolean, true);
    }

    /**
     * Add a where condition from this field.
     *
     * @param  Builder         $builder
     * @param  OperatorElement $operator
     * @param  mixed           $value
     * @param  string          $boolean
     * @return Builder
     */
    public function where(Builder $builder, OperatorElement $operator=null, $value=null, string $boolean='and'): Builder
    {
        if ($operator->needs === 'collection') {
            return $this->whereIn($builder, $value, $boolean, ($operator === Operator::notIn()));
        }

        return $this->getAttribute('id')->addBuilderOperation($builder, 'where', $operator, $value, $boolean);
    }
}
