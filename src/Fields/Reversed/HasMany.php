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

use Laramore\Elements\OperatorElement;
use Laramore\Fields\BaseField;
use Laramore\Contracts\{
    Field\ManyRelationField, Eloquent\LaramoreModel, Eloquent\LaramoreBuilder, Eloquent\LaramoreCollection
};
use Laramore\Facades\Operator;
use Laramore\Traits\Field\HasOneRelation;

class HasMany extends BaseField implements ManyRelationField
{
    use HasOneRelation {
        HasOneRelation::cast as public castModel;
    }

    /**
     * Cast the value to a correct collection.
     *
     * @param mixed $value
     * @return LaramoreCollection
     */
    public function cast($value)
    {
        if ($value instanceof LaramoreCollection) {
            return $value;
        }

        if (\is_null($value) || \is_array($value)) {
            return collect($value);
        }

        return collect($this->castModel($value));
    }

    /**
     * Add a where in condition from this field.
     *
     * @param  LaramoreBuilder    $builder
     * @param  LaramoreCollection $value
     * @param  string             $boolean
     * @param  boolean            $notIn
     * @return LaramoreBuilder
     */
    public function whereIn(LaramoreBuilder $builder, LaramoreCollection $value=null,
                            string $boolean='and', bool $notIn=false): LaramoreBuilder
    {
        $attname = $this->getTargetModel()::getMeta()->getPrimary()->attname;

        return $this->whereNull($builder, $value, $boolean, $notIn, function ($query) use ($attname, $value) {
            return $query->whereIn($attname, $value);
        });
    }

    /**
     * Add a where not in condition from this field.
     *
     * @param  LaramoreBuilder    $builder
     * @param  LaramoreCollection $value
     * @param  string             $boolean
     * @return LaramoreBuilder
     */
    public function whereNotIn(LaramoreBuilder $builder, LaramoreCollection $value=null, string $boolean='and'): LaramoreBuilder
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
        $attname = $this->on::getMeta()->getPrimary()->getNative();

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
        $relation = $model->hasMany(
            $this->getTargetModel(),
            $this->getTarget()->getAttribute()->getNative(),
            $this->getSource()->getAttribute()->getNative()
        );

        if ($this->hasProperty('when')) {
            return (\call_user_func($this->when, $relation, $model) ?? $relation);
        }

        return $relation;
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
        if (!$model->exists) {
            return $value;
        }

        $modelClass = $this->getTargetModel();
        $foreignAttname = $this->getTarget()->getAttribute()->getNative();

        $primaryField = $modelClass::getMeta()->getPrimary()->getAttribute();
        $primaryAttname = $primaryField->getNative();

        $foreignId = $model->getKey();
        $valueIds = $value->map(function ($subModel) use ($primaryAttname) {
            return $subModel->getAttribute($primaryAttname);
        });
        $default = \is_null($this->getDefault()) ? null : $this->getDefault()->getAttribute($foreignAttname);

        $primaryField->addBuilderOperation(
            $modelClass::where($foreignAttname, Operator::equal(), $foreignId),
            'whereNotIn',
            $valueIds
        )->update([$foreignAttname => $default]);

        $primaryField->addBuilderOperation(
            (new $modelClass)->newQuery(),
            'whereIn',
            $valueIds
        )->update([$foreignAttname => $foreignId]);

        return $value;
    }
}
