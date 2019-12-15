<?php
/**
 * Define a reverse foreign field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Collection;
use Laramore\Elements\Operator;
use Laramore\Eloquent\Builder;
use Laramore\Interfaces\{
    IsProxied, IsALaramoreModel
};

class HasMany extends HasOne
{
    public function transformToModel($value)
    {
        return parent::transform($value);
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

        return $this->whereNotNull($builder, $value, $boolean, $operator, ($count ?? \count($value)), function ($query) use ($attname, $value) {
            return $query->whereIn($attname, $value);
        });
    }

    public function consume(IsALaramoreModel $model, $value)
    {
        $relationName = $this->getReversed()->name;
        $collections = collect();

        foreach ($value as $element) {
            if ($element instanceof $this->on) {
                $collections->add($element);
            } else {
                $collections->add($element = $this->transformToModel($element));
            }

            $element->setAttribute($relationName, $model);
        }

        return $collections;
    }

    public function relate(IsProxied $model)
    {
        return $model->hasMany($this->on, $this->to, $this->from);
    }

    public function reverbate(IsALaramoreModel $model, $value): bool
    {
        $attname = $this->on::getMeta()->getPrimary()->attname;
        $id = $model->getKey();
        $ids = $value->map(function ($element) use ($attname) {
            return $element[$attname];
        });

        $this->on::where($this->to, $id)->whereNotIn($attname, $ids)->update([$this->to => null]);
        $this->on::whereIn($attname, $ids)->update([$this->to => $id]);

        return true;
    }
}
