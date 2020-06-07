<?php
/**
 * Add multiple methods for many/one to one relations.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Illuminate\Support\Collection;
use Laramore\Elements\OperatorElement;
use Laramore\Facades\{
    Operator, Option
};
use Laramore\Fields\Constraint\FieldConstraintHandler;
use Laramore\Contracts\Eloquent\{
    LaramoreModel, LaramoreBuilder
};
use Laramore\Contracts\Field\{
    Field, RelationField, Constraint\Constraint
};

trait ToSingleOneRelation
{
    use ModelRelation, Constraints;

    /**
     * Model the relation is on.
     *
     * @var LaramoreModel
     */
    protected $targetModel;

    /**
     * Return the reversed field.
     *
     * @return RelationField
     */
    public function getReversed(): RelationField
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
     * Define the model on which to point.
     *
     * @param string $model
     * @param string $reversedName
     * @param string $relationName
     * @return self
     */
    public function on(string $model, string $reversedName=null, string $relationName=null)
    {
        $this->defineProperty('targetModel', $model);

        if ($model === 'self') {
            $this->addOption(Option::nullable());
        } else {
            $this->getField('reversed')->setMeta($model::getMeta());
        }

        if (!\is_null($reversedName)) {
            $this->reversedName($reversedName);
        }

        if (!\is_null($relationName)) {
            $this->relationName($relationName);
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
        $this->needsToBeUnowned();

        return $this->on('self');
    }

    /**
     * Indicate if it is a relation on itself.
     *
     * @return boolean
     */
    public function isOnSelf()
    {
        $model = $this->getTargetModel();

        return $model === $this->getMeta()->getModelClass() || $model === 'self';
    }

    /**
     * Define the reversed name of the relation.
     *
     * @param string $reversedName
     * @return self
     */
    public function reversedName(string $reversedName)
    {
        $this->needsToBeUnlocked();

        $this->templates['reversed'] = $reversedName;

        return $this;
    }

    /**
     * Define the relation name of the relation.
     *
     * @param string $relationName
     * @return self
     */
    public function relationName(string $relationName)
    {
        $this->needsToBeUnlocked();

        $this->templates['relation'] = $relationName;

        return $this;
    }

    /**
     * Indicate if the relation is head on or not.
     * Usefull to know which to use between source and target.
     *
     * @return boolean
     */
    public function isRelationHeadOn(): bool
    {
        return true;
    }

    /**
     * Model where the relation is set from.
     *
     * @return string
     */
    public function getSourceModel(): string
    {
        $this->needsToBeOwned();

        return $this->getMeta()->getModelClass();
    }

    /**
     * Return the source of the relation.
     *
     * @return Constraint
     */
    public function getSource(): Constraint
    {
        $this->needsToBeOwned();

        return $this->getSourceModel()::getMeta()
            ->getConstraintHandler()->getSource([$this->getField('id')]);
    }

    /**
     * Model where the relation is set to.
     *
     * @return string
     */
    public function getTargetModel(): string
    {
        $this->needsToBeOwned();

        return $this->targetModel;
    }

    /**
     * Return the target of the relation.
     *
     * @return Constraint
     */
    public function getTarget(): Constraint
    {
        $this->needsToBeOwned();

        return $this->getTargetModel()::getMeta()->getPrimary();
    }

    /**
     * Define on, off and from variables after being owned.
     *
     * @return void
     */
    protected function owned()
    {
        if ($this->getTargetModel() === 'self') {
            $this->on($this->getSourceModel());
        }

        parent::owned();

        $this->foreign($this->templates['relation'] ?? null, $this->getTarget()->getMainAttribute());
    }

    /**
     * Check all options.
     *
     * @return void
     */
    protected function checkOptions()
    {
        if (!$this->getTargetModel()) {
            throw new \Exception('Related model settings needed. Set it by calling `on` method');
        }

        $this->templates['reversed'] = $this->getReversed()->name;

        parent::checkOptions();
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
        $name = $this->getTarget()->getMainAttribute()->getName();

        return isset($value[$name]) ? $value[$name] : $value;
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
     * Transform the value to correspond to the field desire.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        $model = $this->getTargetModel();
        $name = $this->getTarget()->getMainAttribute()->getName();

        if (\is_null($value) || $value instanceof $model || \is_array($value) || $value instanceof Collection) {
            return $value;
        }

        $model = new $model;
        $model->setAttributeValue($name, $value);

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
     * Reverbate the relation into database or other fields.
     * It should be called by the set method.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function reverbate(LaramoreModel $model, $value)
    {
        $this->getField('id')->set(
            $model,
            \is_null($value) ? null : $this->getTarget()->getMainAttribute()->get($value)
        );

        return $value;
    }

    /**
     * Return the query with this field as condition.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function relate(LaramoreModel $model)
    {
        $relation = $model->belongsTo(
            $this->getTargetModel(),
            $this->getSource()->getMainAttribute()->getNative(),
            $this->getTarget()->getMainAttribute()->getNative()
        );

        if ($this->hasProperty('when')) {
            return (\call_user_func($this->when, $relation, $model) ?? $relation);
        }

        return $relation;
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

    /**
     * Return the set value for a specific field.
     *
     * @param Field         $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return mixed
     */
    public function setFieldValue(Field $field, LaramoreModel $model, $value)
    {
        if ($model->hasAttributeValue($field->getName()) && $this->getFieldValue($field, $model) !== $value) {
            $this->reset($model);
        }

        return parent::setFieldValue($field, $model, $value);
    }
}
