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
use Laramore\Fields\BaseAttribute;
use Laramore\Contracts\{
    Eloquent\LaramoreModel, Eloquent\LaramoreBuilder, Proxied
};

trait ManyToManyRelation
{
    use ModelRelation;

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
     * Pivot meta name.
     *
     * @var string
     */
    protected $pivotMeta;

    /**
     * Pivot to name.
     *
     * @var string
     */
    protected $pivotTo;

    /**
     * Pivot from name.
     *
     * @var string
     */
    protected $pivotFrom;

    /**
     * Pivot name.
     *
     * @var string
     */
    protected $pivotName;

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
     * Transform the value to be used as a correct model.
     *
     * @param  mixed $value
     * @return LaramoreModel
     */
    public function transformToModel($value)
    {
        if ($value instanceof $this->on) {
            return $value;
        }

        $model = new $this->on;
        $model->setAttributeValue($model->getKeyName(), $value);

        return $model;
    }

    /**
     * Transform the value to be used as a correct collection.
     *
     * @param  mixed $value
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
     * Return all pivot attributes.
     *
     * @return array<BaseAttribute>
     */
    public function getPivotAttributes(): array
    {
        return \array_map(function (BaseAttribute $field) {
            return $field->attname;
        }, \array_filter(\array_values($this->getPivotMeta()->getFields()), function (BaseAttribute $field) {
            return $field->visible;
        }));
    }

    /**
     * Return the query with this field as condition.
     *
     * @param  Proxied $model
     * @return mixed
     */
    public function relate(Proxied $model)
    {
        return $model->belongsToMany(
            $this->on, $this->getPivotMeta()->getTableName(), $this->pivotTo->from,
            $this->pivotFrom->from, $this->to, $this->from, $this->name
        )->withPivot($this->getPivotAttributes())->using($this->getPivotMeta()->getModelClass())->as($this->pivotName);
    }

    /**
     * Add a where null condition from this field.
     *
     * @param  LaramoreBuilder       $builder
     * @param  mixed         $value
     * @param  string        $boolean
     * @param  boolean       $notIn
     * @param  \Closure|null $callback
     * @return LaramoreBuilder
     */
    public function whereNull(LaramoreBuilder $builder, $value=null, string $boolean='and',
                              bool $notIn=false, \Closure $callback=null): LaramoreBuilder
    {
        if ($notIn) {
            return $this->whereNotNull($builder, $value, $boolean, null, null, $callback);
        }

        return $builder->doesntHave($this->name, $boolean, $callback);
    }

    /**
     * Add a where not null condition from this field.
     *
     * @param  LaramoreBuilder       $builder
     * @param  mixed         $value
     * @param  string        $boolean
     * @param  mixed         $operator
     * @param  integer       $count
     * @param  \Closure|null $callback
     * @return LaramoreBuilder
     */
    public function whereNotNull(LaramoreBuilder $builder, $value=null, string $boolean='and', $operator=null,
                                 int $count=1, \Closure $callback=null): LaramoreBuilder
    {
        return $builder->has($this->name, (string) ($operator ?? Operator::supOrEq()), $count, $boolean, $callback);
    }

    /**
     * Add a where in condition from this field.
     *
     * @param  LaramoreBuilder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @param  boolean    $notIn
     * @return LaramoreBuilder
     */
    public function whereIn(LaramoreBuilder $builder, Collection $value=null, string $boolean='and', bool $notIn=false): LaramoreBuilder
    {
        $attname = $this->on::getMeta()->getPrimary()->attname;

        return $this->whereNull($builder, $value, $boolean, $notIn, function ($query) use ($attname, $value) {
            return $query->whereIn($attname, $value);
        });
    }

    /**
     * Add a where not in condition from this field.
     *
     * @param  LaramoreBuilder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @return LaramoreBuilder
     */
    public function whereNotIn(LaramoreBuilder $builder, Collection $value=null, string $boolean='and'): LaramoreBuilder
    {
        return $this->whereIn($builder, $value, $boolean, true);
    }

    /**
     * Add a where condition from this field.
     *
     * @param  LaramoreBuilder         $builder
     * @param  OperatorElement $operator
     * @param  mixed           $value
     * @param  string          $boolean
     * @param  integer         $count
     * @return LaramoreBuilder
     */
    public function where(LaramoreBuilder $builder, OperatorElement $operator, $value=null,
                          string $boolean='and', int $count=null): LaramoreBuilder
    {
        $attname = $this->on::getMeta()->getPrimary()->attname;

        return $this->whereNotNull($builder, $value, $boolean, $operator, ($count ?? count($value)),
            function ($query) use ($attname, $value) {
                return $query->whereIn($attname, $value);
            }
        );
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

    /**
     * Reverbate the relation into database.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return boolean
     */
    public function reverbate(LaramoreModel $model, $value): bool
    {
        $this->sync($model, $value);

        return true;
    }

    /**
     * Attach value to the model relation.
     *
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return LaramoreModel
     */
    public function attach(LaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->attach($value);

        return $model;
    }

    /**
     * Detach value from the model relation.
     *
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return LaramoreModel
     */
    public function detach(LaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->detach($value);

        return $model;
    }

    /**
     * Sync value with the model relation.
     *
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return LaramoreModel
     */
    public function sync(LaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->sync($value);

        return $model;
    }

    /**
     * Toggle value to the model relation.
     *
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return LaramoreModel
     */
    public function toggle(LaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->toggle($value);

        return $model;
    }

    /**
     * Sync without detaching value from the model relation.
     *
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return LaramoreModel
     */
    public function syncWithoutDetaching(LaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->syncWithoutDetaching($value);

        return $model;
    }

    /**
     * Update existing pivot for the value with the model relation.
     *
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return LaramoreModel
     */
    public function updateExistingPivot(LaramoreModel $model, $value)
    {
        \call_user_func([$model, $this->name])->updateExistingPivot($value);

        return $model;
    }
}
