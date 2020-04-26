<?php
/**
 * Define a reverse OneToMany field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Reversed;

use Illuminate\Support\Collection;
use Laramore\Elements\OperatorElement;
use Laramore\Contracts\Field\RelationField;
use Laramore\Fields\BaseField;
use Laramore\Contracts\{
    Eloquent\LaramoreModel, Eloquent\LaramoreBuilder
};
use Laramore\Traits\Field\HasOneRelation;

class HasMany extends BaseField implements RelationField
{
    use HasOneRelation {
        HasOneRelation::transform as protected transformToModel;
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
     * @param  LaramoreBuilder $builder
     * @param  Collection      $value
     * @param  string          $boolean
     * @param  boolean         $notIn
     * @return LaramoreBuilder
     */
    public function whereIn(LaramoreBuilder $builder, Collection $value=null,
                            string $boolean='and', bool $notIn=false): LaramoreBuilder
    {
        $attname = $this->on::getMeta()->getPrimary()->attname;

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
        $attname = $this->on::getMeta()->getPrimary()->attname;

        return $this->whereNotNull($builder, $value, $boolean, $operator, ($count ?? \count($value)),
            function ($query) use ($attname, $value) {
                return $query->whereIn($attname, $value);
            }
        );
    }

    /**
     * Return the relation with this field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function relate(LaramoreModel $model)
    {
        return $model->hasMany(
            $this->getSourceModel(),
            $this->getSourceAttribute()->getNative(),
            $this->getTargetAttribute()->getNative()
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

        return $value;
    }
}
