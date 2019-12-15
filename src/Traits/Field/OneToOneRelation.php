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
use Laramore\Elements\Operator;
use Laramore\Eloquent\Builder;
use Laramore\Facades\{
    Metas, Operators
};
use Laramore\Fields\LinkField;
use Laramore\Interfaces\IsALaramoreModel;

trait OneToOneRelation
{
    protected $on;
    protected $to;
    protected $off;
    protected $from;
    protected $reversedName;
    protected $relationName;

    public function getReversed(): LinkField
    {
        return $this->getLink('reversed');
    }

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

    public function onSelf()
    {
        return $this->on('self');
    }

    public function to(string $name)
    {
        $this->needsToBeUnlocked();

        $this->defineProperty('to', $this->getReversed()->from = $name);

        return $this;
    }

    public function reversedName(string $reversedName=null)
    {
        $this->needsToBeUnowned();
        $this->needsToBeUnlocked();

        $this->linksName['reversed'] = $reversedName;

        return $this;
    }

    public function owned()
    {
        if ($this->on === 'self') {
            $this->on($this->getMeta()->getModelClass());
        }

        parent::owned();

        $this->defineProperty('off', $this->getReversed()->on = $this->getMeta()->getModelClass());
        $this->defineProperty('from', $this->getReversed()->to = $this->getAttribute('id')->attname);
    }

    protected function setConstraints()
    {
        parent::setConstraints();
        
        $relationName = $this->hasProperty('relationName') ? $this->getProperty('relationName') : null;

        $this->foreign('id', Metas::get($this->on)->getAttribute($this->to), $relationName);
    }

    protected function checkRules()
    {
        if (!$this->on) {
            throw new \Exception('Related model settings needed. Set it by calling `on` method');
        }

        $this->defineProperty('reversedName', $this->getReversed()->name);

        parent::checkRules();
    }

    public function isOnSelf()
    {
        return $this->on === $this->getMeta()->getModelClass();
    }

    public function cast($value)
    {
        return $this->transform($value);
    }

    public function dry($value)
    {
        $value = $this->transform($value);

        return isset($value[$this->to]) ? $value[$this->to] : $value;
    }

    public function transform($value)
    {
        if (\is_null($value) || $value instanceof $this->on || \is_array($value) || $value instanceof Collection) {
            return $value;
        }

        $model = new $this->on;
        $model->setRawAttribute($this->to, $value);

        return $model;
    }

    public function serialize($value)
    {
        return $value;
    }

    public function retrieve(IsALaramoreModel $model)
    {
        return $this->relate($model)->getResults();
    }

    public function whereNull(Builder $builder, $value=null, $boolean='and', $not=false)
    {
        $builder->getQuery()->whereNull($this->getAttribute('id')->attname, $boolean, $not);

        return $builder;
    }

    public function whereNotNull(Builder $builder, $value=null, $boolean='and')
    {
        return $this->whereNull($builder, $value, $boolean, true);

        return $builder;
    }

    public function whereIn(Builder $builder, Collection $value=null, $boolean='and', $not=false)
    {
        $builder->getQuery()->whereIn($this->getAttribute('id')->attname, $value, $boolean, $not);

        return $builder;
    }

    public function whereNotIn(Builder $builder, Collection $value=null, $boolean='and')
    {
        return $this->whereIn($builder, $value, $boolean, true);
    }

    public function where(Builder $builder, Operator $operator=null, $value=null, $boolean='and')
    {
        if ($operator->needs === 'collection') {
            return $this->whereIn($builder, $value, $boolean, ($operator === Operators::notIn()));
        }

        $builder->getQuery()->where($this->getAttribute('id')->attname, $operator, $value, $boolean);

        return $builder;
    }
}
