<?php
/**
 * Define an attribute field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\{
    Str, Collection
};
use Laramore\Elements\OperatorElement;
use Laramore\Contracts\{
    Field\AttributeField, Eloquent\LaramoreModel, Eloquent\LaramoreBuilder
};
use Laramore\Fields\Constraint\BaseConstraint;
use Laramore\Traits\Field\{
    IndexableConstraints, ForeignConstraints
};

abstract class BaseAttribute extends BaseField implements AttributeField
{
    use IndexableConstraints, ForeignConstraints;

    /**
     * AttributeField name of this field.
     *
     * @var string
     */
    protected $attname;

    /**
     * Parse the attribute attname.
     *
     * @param  string $attname
     * @return string
     */
    public static function parseAttname(string $attname): string
    {
        return Str::replaceInTemplate(config('field.templates.attname'), compact('attname'));
    }

    /**
     * Get the attribute name.
     *
     * @return string
     */
    public function getAttname(): string
    {
        return $this->attname;
    }

    /**
     * Define the name property.
     *
     * @param  string $name
     * @param  string $attname
     * @return self
     */
    protected function setName(string $name, string $attname=null)
    {
        parent::setName($name);

        // If no attribute name have been set by the user, define ours based on the name.
        if (\is_null($this->attname)) {
            $this->setAttname(static::parseAttname($name));
        }

        return $this;
    }

    /**
     * Return the native value of this field.
     * Commonly, its attname.
     *
     * @return string
     */
    public function getNative(): string
    {
        return $this->attname;
    }

    /**
     * Each class locks in a specific way.
     *
     * @return void
     */
    protected function locking()
    {
        parent::locking();

        if ($this->getConstraintHandler()->count(BaseConstraint::FOREIGN) > 0) {
            foreach ($this->getConstraintHandler()->all(BaseConstraint::FOREIGN) as $constraint) {
                // @var RelationConstraint $constraint
                if ($constraint->getSourceAttribute() === $this) {
                    $this->addOptions(\array_merge($constraint->getTargetAttribute()->options, $this->options));
                }
            }
        }
    }

    /**
     * Indicate if the field has a value.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function has(LaramoreModel $model)
    {
        return $model->hasAttributeValue($this->getName());
    }

    /**
     * Get the value definied by the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function get(LaramoreModel $model)
    {
        return $model->getAttributeValue($this->getName());
    }

    /**
     * Set the value for the field.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function set(LaramoreModel $model, $value)
    {
        return $model->setAttributeValue($this->getName(), $value);
    }

    /**
     * Reet the value for the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function reset(LaramoreModel $model)
    {
        if ($this->hasDefault()) {
            $model->setAttributeValue($this->getName(), $value = $this->getDefault());

            return $value;
        }

        $model->unsetAttribute($this->getName());
    }

    /**
     * Add an operation to a query builder.
     *
     * @param LaramoreBuilder $builder
     * @param string          $operation
     * @param mixed           ...$params
     * @return LaramoreBuilder
     */
    public function addBuilderOperation(LaramoreBuilder $builder, string $operation, ...$params): LaramoreBuilder
    {
        \call_user_func([$builder->getQuery(), $operation], $this->getQualifiedName(), ...$params);

        return $builder;
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
        return $this->addBuilderOperation($builder, 'whereNull', $boolean, $not);
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
        return $this->addBuilderOperation($builder, 'whereIn', $value, $boolean, $notIn);
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
        return $this->addBuilderOperation($builder, 'where', $operator, $value, $boolean);
    }
}
