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

use Illuminate\Support\{
    Collection, Str
};
use Laramore\Elements\Operator;
use Laramore\Facades\Operators;
use Laramore\Eloquent\Builder;
use Laramore\Fields\AttributeField;
use Laramore\Interfaces\{
    IsALaramoreModel, IsProxied
};

trait ManyToManyRelation
{
    protected $on;
    protected $to;
    protected $off;
    protected $from;
    protected $pivotMeta;
    protected $pivotTo;
    protected $pivotFrom;

    protected function setForeigns()
    {
    }

    public function cast($value)
    {
        return $this->transform($value);
    }

    public function dry($value)
    {
        return $this->transform($value)->map(function ($value) {
            return $value[$this->from];
        });
    }

    public function transformToModel($value)
    {
        if ($value instanceof $this->on) {
            return $value;
        }

        $model = new $this->on;
        $model->setRawAttribute($model->getKeyName(), $value);

        return $model;
    }

    public function transform($value)
    {
        if ($value instanceof Collection) {
            return $value;
        }

        if (\is_null($value) || \is_array($value)) {
            return collect($value);
        }

        return collect($this->transformToModel($value));
    }

    public function serialize($value)
    {
        return $value;
    }

    public function retrieve(IsALaramoreModel $model)
    {
        return $this->relate($model)->getResults();
    }

    public function getPivotAttributes(): array
    {
        return \array_map(function (AttributeField $field) {
            return $field->attname;
        }, \array_filter(\array_values($this->getPivotMeta()->getAttributes()), function (AttributeField $field) {
            return $field->visible;
        }));
    }

    public function relate(IsProxied $model)
    {
        return $model->belongsToMany($this->on, $this->getPivotMeta()->getTableName(), $this->pivotTo->from, $this->pivotFrom->from, $this->to, $this->from, $this->name)
            ->withPivot($this->getPivotAttributes())->using($this->getPivotMeta()->getModelClass());
    }

    public function whereNull(Builder $builder, $value=null, $boolean='and', $not=false, \Closure $callback=null)
    {
        if ($not) {
            return $this->whereNotNull($builder, $value, $boolean, null, null, $callback);
        }

        return $builder->doesntHave($this->name, $boolean, $callback);
    }

    public function whereNotNull(Builder $builder, $value=null, $boolean='and', $operator=null, int $count=1, \Closure $callback=null)
    {
        return $builder->has($this->name, (string) ($operator ?? Operators::supOrEq()), $count, $boolean, $callback);
    }

    public function whereIn(Builder $builder, Collection $value=null, $boolean='and', $not=false)
    {
        $attname = $this->on::getMeta()->getPrimary()->attname;

        return $this->whereNull($builder, $value, $boolean, $not, function ($query) use ($attname, $value) {
            return $query->whereIn($attname, $value);
        });
    }

    public function whereNotIn(Builder $builder, Collection $value=null, $boolean='and')
    {
        return $this->whereIn($builder, $value, $boolean, true);
    }

    public function where(Builder $builder, Operator $operator, $value=null, $boolean='and', int $count=null)
    {
        $attname = $this->on::getMeta()->getPrimary()->attname;

        return $this->whereNotNull($builder, $value, $boolean, $operator, ($count ?? count($value)), function ($query) use ($attname, $value) {
            return $query->whereIn($attname, $value);
        });
    }

    public function consume(IsALaramoreModel $model, $value)
    {
        $relationName = $this->getReversed()->name;
        $collections = collect();

        foreach ($value as $element) {
            if ($element instanceof $this->on) {
                $collections->push($element);
            } else {
                $collections->push($element = $this->transformToModel($element));
            }

            $element->setRelation($relationName, $model);
        }

        return $collections;
    }

    public function reverbate(IsALaramoreModel $model, $value): bool
    {
        $this->sync($model, $value);

        return true;
    }

    public function attach(IsALaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->attach($value);

        return $model;
    }

    public function detach(IsALaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->detach($value);

        return $model;
    }

    public function sync(IsALaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->sync($value);

        return $model;
    }

    public function toggle(IsALaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->toggle($value);

        return $model;
    }

    public function syncWithoutDetaching(IsALaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->syncWithoutDetaching($value);

        return $model;
    }

    public function updateExistingPivot(IsALaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->updateExistingPivot($value);

        return $model;
    }
}
