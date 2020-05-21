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
use Laramore\Contracts\{
    Eloquent\LaramoreModel, Eloquent\LaramoreBuilder, Field\AttributeField
};

trait ManyToManyRelation
{
    use ModelRelation;

    /**
     * Dry the value in a simple format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function dry($value)
    {
        $attribute = $this->getTargetAttribute();

        return $this->transform($value)->map(function ($model) use ($attribute) {
            return $attribute->get($model);
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
    public function transformModel($value)
    {
        $model = $this->getTargetModel();

        if ($value instanceof $model) {
            return $value;
        }

        $model = new $model;
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

        return collect($this->transformModel($value));
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
     * Reverbate the relation into database or other fields.
     * It should be called by the set method.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function reverbate(LaramoreModel $model, $value)
    {
        if ($model->exists) {
            $this->relate($model)->sync($value);
        }

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
     * @return array<AttributeField>
     */
    public function getPivotAttributes(): array
    {
        $fields = \array_values($this->getPivotMeta()->getFields(AttributeField::class));

        $fields = \array_filter($fields, function (AttributeField $field) {
            return $field->visible;
        });

        return \array_map(function (AttributeField $field) {
            return $field->getNative();
        }, $fields);
    }

    /**
     * Return the relation with this field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function relate(LaramoreModel $model)
    {
        return $model->belongsToMany(
            $this->getTargetModel(),
            $this->getPivotMeta()->getTableName(),
            $this->getPivotSource()->getSourceAttribute()->getNative(),
            $this->getPivotTarget()->getSourceAttribute()->getNative(),
            $this->getSourceAttribute()->getNative(),
            $this->getTargetAttribute()->getNative(),
            $this->getName()
        )->withPivot($this->getPivotAttributes())
            ->using($this->getPivotMeta()->getModelClass())
            ->as($this->getPivotName());
    }

    /**
     * Add a where null condition from this field.
     *
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @param  boolean         $notIn
     * @param  \Closure|null   $callback
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
     * @param  LaramoreBuilder $builder
     * @param  mixed           $value
     * @param  string          $boolean
     * @param  mixed           $operator
     * @param  integer         $count
     * @param  \Closure|null   $callback
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
     * @param  LaramoreBuilder $builder
     * @param  Collection      $value
     * @param  string          $boolean
     * @param  boolean         $notIn
     * @return LaramoreBuilder
     */
    public function whereIn(LaramoreBuilder $builder, Collection $value=null,
                            string $boolean='and', bool $notIn=false): LaramoreBuilder
    {
        $attname = $this->getSource()::getMeta()->getPrimary()->getAttname();

        return $this->whereNull($builder, $value, $boolean, $notIn, function ($query) use ($attname, $value) {
            return $query->whereIn($attname, $value);
        });
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
     * @param  integer         $count
     * @return LaramoreBuilder
     */
    public function where(LaramoreBuilder $builder, OperatorElement $operator, $value=null,
                          string $boolean='and', int $count=null): LaramoreBuilder
    {
        $attname = $this->getSource()::getMeta()->getPrimary()->getAttname();

        return $this->whereNotNull($builder, $value, $boolean, $operator, ($count ?? count($value)),
            function ($query) use ($attname, $value) {
                return $query->whereIn($attname, $value);
            }
        );
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
        $this->relate($model)->attach($value);

        $model->unsetRelation($this->getNative());

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
        $this->relate($model)->detach($value);

        $model->unsetRelation($this->getNative());

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
        $this->set($model, $value);

        $model->unsetRelation($this->getNative());

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
        $this->relate($model)->toggle($value);

        $model->unsetRelation($this->getNative());

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
        $this->relate($model)->syncWithoutDetaching($value);

        $model->unsetRelation($this->getNative());

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
        $this->relate($model)->updateExistingPivot($value);

        $model->unsetRelation($this->getNative());

        return $model;
    }
}
