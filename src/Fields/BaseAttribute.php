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

use Illuminate\Container\Container;
use Illuminate\Support\{
    Str, Collection
};
use Laramore\Elements\OperatorElement;
use Laramore\Contracts\{
    Field\AttributeField, Eloquent\LaramoreModel, Eloquent\Builder
};
use Laramore\Traits\Field\HasConstraints;

abstract class BaseAttribute extends BaseField implements AttributeField
{
    use HasConstraints {
        HasConstraints::owned as protected ownConstraintHandler;
    }

    /**
     * AttributeField name of this field.
     *
     * @var string
     */
    protected $attname;

    /**
     * Field whose this field is linked to.
     * If this field is a fk on another field, just share its options.
     *
     * TODO: Utiliser constraint fk
     * @var AttributeField
     */
    protected $sharedField;

    /**
     * Create a new field with basic options.
     * The constructor is protected so the field is created writing left to right.
     * ex: Char::field()->maxLength(255) insteadof (new Char)->maxLength(255).
     *
     * @param array|null $options
     */
    protected function __construct(array $options=null)
    {
        parent::__construct($options);

        $this->setConstraintHandler();
    }

    /**
     * Parse the attribute attname.
     *
     * @param  string $attname
     * @return string
     */
    public static function parseAttname(string $attname): string
    {
        return Str::replaceInTemplate(Container::getInstance()->config->get('field.templates.attname'), compact('attname'));
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
     * Define a shared field.
     * Usefull to link this attribute as fk to another.
     *
     * @param AttributeField $field
     * @return self
     */
    public function sharedField(AttributeField $field)
    {
        $this->needsToBeOwned();
        $this->needsToBeUnlocked();

        if (!($this->getOwner() instanceof BaseComposed)) {
            throw new \Exception('Only attributes owned by a composed field can have a shared field');
        }

        $this->defineProperty('sharedField', $field);

        return $this;
    }

    /**
     * Each class locks in a specific way.
     *
     * @return void
     */
    protected function locking()
    {
        parent::locking();

        if ($this->hasProperty('sharedField')) {
            $this->updateFromSharedField($this->sharedField);
        }
    }

    /**
     * Own the constraint handler.
     *
     * @return void
     */
    protected function owned()
    {
        parent::owned();

        $this->ownConstraintHandler();
    }

    /**
     * Update a field
     *
     * @param BaseField $field
     *
     * @return void
     */
    protected function updateFromSharedField(BaseField $field)
    {
        $this->addOptions(\array_merge($field->options, $this->options));
    }

    /**
     * Get the value definied by the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function get(LaramoreModel $model)
    {
        return $model->getAttributeValue($this->getNative());
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
        return $model->setAttributeValue($this->getNative(), $value);
    }

    /**
     * Reet the value for the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function reset(LaramoreModel $model)
    {
        return $model->setAttributeValue($this->getNative(), $this->getDefault());
    }

    /**
     * Add an operation to a query builder.
     *
     * @param Builder $builder
     * @param string  $operation
     * @param mixed   ...$params
     * @return Builder
     */
    public function addBuilderOperation(Builder $builder, string $operation, ...$params): Builder
    {
        \call_user_func([$builder->getQuery(), $operation], $this->getFullName(), ...$params);

        return $builder;
    }

    /**
     * Add a where null condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @param  boolean $not
     * @return Builder
     */
    public function whereNull(Builder $builder, $value=null, string $boolean='and', bool $not=false): Builder
    {
        return $this->addBuilderOperation($builder, 'whereNull', $boolean, $not);
    }

    /**
     * Add a where not null condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @return Builder
     */
    public function whereNotNull(Builder $builder, $value=null, string $boolean='and'): Builder
    {
        return $this->whereNull($builder, $value, $boolean, true);
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
        return $this->addBuilderOperation($builder, 'whereIn', $value, $boolean, $notIn);
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
     * @return Builder
     */
    public function where(Builder $builder, OperatorElement $operator, $value=null, string $boolean='and'): Builder
    {
        return $this->addBuilderOperation($builder, 'where', $operator, $value, $boolean);
    }
}
