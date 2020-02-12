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
use Laramore\Elements\OperatorElement;
use Laramore\Eloquent\Builder;
use Laramore\Interfaces\{
    IsProxied, IsALaramoreModel
};

class HasMany extends HasOne
{
    /**
     * Transform the value to be used as a correct model.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transformToModel($value)
    {
        return parent::transform($value);
    }

    /**
     * Transform the value to a correct collection.
     *
     * @param mixed $value
     * @return Collection
     */
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
        $attname = $this->on::getMeta()->getPrimary()->attname;

        return $this->whereNull($builder, $value, $boolean, $notIn, function ($query) use ($attname, $value) {
            return $query->whereIn($attname, $value);
        });
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
     * @param  integer         $count
     * @return Builder
     */
    public function where(Builder $builder, OperatorElement $operator, $value=null,
                          string $boolean='and', int $count=null): Builder
    {
        $attname = $this->on::getMeta()->getPrimary()->attname;

        return $this->whereNotNull($builder, $value, $boolean, $operator, ($count ?? \count($value)),
            function ($query) use ($attname, $value) {
                return $query->whereIn($attname, $value);
            }
        );
    }

    /**
     * Use the relation to set the other field values.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return mixed
     */
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

    /**
     * Return the query with this field as condition.
     *
     * @param  IsALaramoreModel $model
     * @return Builder
     */
    public function relate(IsALaramoreModel $model)
    {
        return $model->hasMany($this->on, $this->to, $this->from);
    }

    /**
     * Reverbate the relation into database.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return boolean
     */
    public function reverbate(IsALaramoreModel $model, $value): bool
    {
        $primary = $this->on::getMeta()->getPrimary();
        $attname = $primary->getNative();
        $id = $model->getKey();
        $valueIds = $value->map(function ($element) use ($attname) {
            return $element[$attname];
        });

        $primary->addBuilderOperation(
            $this->on::where($this->to, $id),
            'whereNotIn',
            $valueIds
        )->update([$this->to => null]);

        $primary->addBuilderOperation(
            (new $this->on)->newQuery(),
            'whereIn',
            $valueIds
        )->update([$this->to => $id]);

        return true;
    }
}
