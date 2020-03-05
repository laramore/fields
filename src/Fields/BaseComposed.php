<?php
/**
 * Define a composed field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\{
    Str, Arr, Facades\Event
};
use Laramore\Elements\OptionElement;
use Laramore\Exceptions\ConfigException;
use Laramore\Facades\Option;
use Laramore\Contracts\{
    Proxied, Eloquent\LaramoreModel
};
use Laramore\Contracts\Field\{
    Field, ComposedField, AttributeField, RelationField, ExtraField
};

abstract class BaseComposed extends BaseField implements ComposedField
{
    /**
     * AttributeField fields managed by this composed fields.
     *
     * @var array<BaseAttribute>
     */
    protected $fields = [];

    /**
     * Name of each field.
     *
     * @var array<string>
     */
    protected $fieldsName = [];

    /**
     * Create a new field with basic options.
     * The constructor is protected so the field is created writing left to right.
     * ex: OneToMany::field()->on(User::class) insteadof (new OneToMany)->on(User::class).
     *
     * @param array $options
     * @param array $fields  Allow the user to define sub fields.
     */
    protected function __construct(array $options=null, array $fields=null)
    {
        parent::__construct($options);

        $fields = ($fields ?: $this->getConfig('fields'));

        if (\is_null($fields) || (\count($fields) && !Arr::isAssoc($fields))) {
            throw new ConfigException($this->getConfigPath('fields'), 'any associative array of fields', $fields);
        }

        foreach ($fields as $name => $field) {
            if (!\is_string($name)) {
                throw new \Exception('Fields need names');
            }

            $this->createField($name, $field);
        }
    }

    /**
     * Call the constructor and generate the field.
     *
     * @param array $options
     * @param array $fields  Allow the user to define sub fields.
     * @return self
     */
    public static function field(array $options=null, array $fields=null)
    {
        $creating = Event::until('fields.creating', static::class, \func_get_args());

        if ($creating === false) {
            return null;
        }

        $field = $creating ?: new static($fields);

        Event::dispatch('fields.created', $field);

        return $field;
    }

    /**
     * Create a field.
     *
     * @param  string             $name
     * @param  array|string|Field $fieldData
     * @return Field
     */
    protected function createField(string $name, $fieldData): Field
    {
        if (\is_array($fieldData)) {
            $field = $fieldData[0]::field($fieldData[1]);
        } else if (\is_string($fieldData)) {
            $field = $fieldData::field();
        }

        $this->setField($name, $field);

        return $field;
    }

    /**
     * Define a field with a given name.
     * Be carefull of how it is used.
     *
     * @param string $name
     * @param Field  $field
     * @return self
     */
    public function setField(string $name, Field $field)
    {
        $this->needsToBeUnlocked();

        $this->fields[$name] = $field;

        return $this;
    }

    /**
     * Indicate if this composed has a field.
     *
     * @param  string $name
     * @param  string $class The field must be an instance of the class.
     * @return boolean
     */
    public function hasField(string $name, string $class=null): bool
    {
        return isset($this->getFields()[$name]) && (
            \is_null($class) || ($this->getFields()[$name] instanceof $class)
        );
    }

    /**
     * Return the field with the given name.
     *
     * @param  string $name
     * @param  string $class The field must be an instance of the class.
     * @return Field
     */
    public function getField(string $name, string $class=null): Field
    {
        if ($this->hasField($name, $class)) {
            return $this->getFields()[$name];
        } else {
            throw new \Exception("The field `$name` does not exist");
        }
    }

    /**
     * Return all fields.
     *
     * @param  string $class The field must be an instance of the class.
     * @return array<Field>
     */
    public function getFields(string $class=null): array
    {
        if (!\is_null($class)) {
            return \array_filter($this->fields, function ($field) use ($class) {
                return $field instanceof $class;
            });
        }

        return $this->fields;
    }

    /**
     * Add a option to the resource.
     *
     * @param string|OptionElement $option
     * @return self
     */
    protected function addOption($option)
    {
        if (\is_string($option)) {
            $option = Option::get($option);
        }

        if (!$option->has('heritable') || $option->heritable !== false) {
            foreach ($this->getFields(AttributeField::class) as $attribute) {
                $attribute->addOption($option);
            }
        }

        return parent::addOption($option);
    }

    /**
     * Remove a option from the resource.
     *
     * @param  string|OptionElement $option
     * @return self
     */
    protected function removeOption($option)
    {
        if (\is_string($option)) {
            $option = Option::get($option);
        }

        foreach ($this->getFields(AttributeField::class) as $attribute) {
            $attribute->removeOption($option);
        }

        return parent::removeOption($option);
    }

    /**
     * Callaback when the instance is owned.
     *
     * @return void
     */
    public function owned()
    {
        parent::owned();

        $this->ownFields();
    }

    /**
     * Own each fields.
     *
     * @return void
     */
    protected function ownFields()
    {
        foreach ($this->fields as $key => $field) {
            $template = ($this->fieldsName[$key] ?? $this->getConfig("templates.$key"));
            $name = $this->replaceInFieldTemplate($template, $key);

            $this->fields[$key] = $field->own($this, $name);
        }
    }

    /**
     * Replace in field template
     *
     * @param string $template
     * @param string $fieldname
     * @return string
     */
    protected function replaceInFieldTemplate(string $template, string $fieldname)
    {
        $keyValues = [
            'modelname' => static::parseName($this->getMeta()->getModelClassName()),
            'identifier' => $fieldname,
            'name' => $this->getName(),
        ];

        return Str::replaceInTemplate($template, $keyValues);
    }

    /**
     * Each class locks in a specific way.
     *
     * @return void
     */
    protected function locking()
    {
        if (\count($this->fields) === 0) {
            throw new \Exception('A composed field needs at least one field');
        }

        $this->lockFields();

        parent::locking();
    }

    /**
     * Lock each sub attributes.
     *
     * @return void
     */
    protected function lockFields()
    {
        foreach ($this->fields as $field) {
            $field->lock();
        }
    }

    /**
     * Return the get value for a specific field.
     *
     * @param Field         $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function getFieldValue(Field $field, LaramoreModel $model)
    {
        return $this->getOwner()->getFieldValue($field, $model);
    }

    /**
     * Return the set value for a specific field.
     * z
     * @param Field         $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return mixed
     */
    public function setFieldValue(Field $field, LaramoreModel $model, $value)
    {
        return $this->getOwner()->setFieldValue($field, $model, $value);
    }

    /**
     * Reset the value with the default value for a specific field.
     *
     * @param Field         $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function resetFieldValue(Field $field, LaramoreModel $model)
    {
        return $this->getOwner()->resetFieldValue($field, $model);
    }

    /**
     * Return the get value for a relation field.
     *
     * @param RelationField $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function relateFieldValue(RelationField $field, LaramoreModel $model)
    {
        return $this->getOwner()->relateFieldValue($field, $model);
    }

    /**
     * Return the get value for a relation field.
     *
     * @param ExtraField    $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function retrieveFieldValue(ExtraField $field, LaramoreModel $model)
    {
        return $this->getOwner()->retrieveFieldValue($field, $model);
    }

    /**
     * Reverbate a saved relation value for a specific field.
     *
     * @param RelationField $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return boolean
     */
    public function reverbateFieldValue(RelationField $field, LaramoreModel $model, $value): bool
    {
        return $this->getOwner()->reverbateFieldValue($attribute, $model, $value);
    }

    /**
     * Return generally a Builder after adding to it a condition.
     *
     * @param Field                $attribute
     * @param Proxied              $builder
     * @param Operator|string|null $operator
     * @param mixed                $value
     * @param mixed                ...$args
     * @return mixed
     */
    public function whereFieldValue(Field $attribute, Proxied $builder, $operator=null, $value=null, ...$args)
    {
        if (func_num_args() === 3) {
            return $this->getOwner()->whereFieldValue($attribute, $builder, $operator);
        }

        return $this->getOwner()->whereFieldValue($attribute, $builder, $operator, $value, ...$args);
    }

    /**
     * Transform a value for a specific field.
     *
     * @param Field $attribute
     * @param mixed $value
     * @return mixed
     */
    public function transformFieldValue(Field $attribute, $value)
    {
        return $this->getOwner()->transformFieldValue($attribute, $value);
    }

    /**
     * Serialize a value for a specific field.
     *
     * @param Field $attribute
     * @param mixed $value
     * @return mixed
     */
    public function serializeFieldValue(Field $attribute, $value)
    {
        return $this->getOwner()->serializeFieldValue($attribute, $value);
    }

    /**
     * Check if the value is correct for a specific field.
     *
     * @param Field $attribute
     * @param mixed $value
     * @return mixed
     */
    public function checkFieldValue(Field $attribute, $value)
    {
        return $this->getOwner()->checkFieldValue($attribute, $value);
    }

    /**
     * Dry a value for a specific field.
     *
     * @param Field $attribute
     * @param mixed $value
     * @return mixed
     */
    public function dryFieldValue(Field $attribute, $value)
    {
        return $this->getOwner()->dryFieldValue($attribute, $value);
    }

    /**
     * Cast a value for a specific field.
     *
     * @param Field $attribute
     * @param mixed $value
     * @return mixed
     */
    public function castFieldValue(Field $attribute, $value)
    {
        return $this->getOwner()->castFieldValue($attribute, $value);
    }

    /**
     * Return the default value for a specific field.
     *
     * @param Field $attribute
     * @return mixed
     */
    public function defaultFieldValue(Field $attribute)
    {
        return $this->getOwner()->defaultFieldValue($attribute);
    }

    /**
     * Call a field attribute method that is not basic.
     *
     * @param  Field  $attribute
     * @param  string $methodName
     * @param  array  $args
     * @return mixed
     */
    public function callFieldValueMethod(Field $attribute, string $methodName, array $args)
    {
        return $this->getOwner()->callFieldValueMethod($attribute, $methodName, $args);
    }

    /**
     * Set a field with a given name.
     *
     * @param string $method
     * @param array  $args
     * @return self
     */
    public function __call(string $method, array $args)
    {
        if (static::hasMacro($method)) {
            return $this->callMacro($method, $args);
        }

        if (\preg_match('/^(.*)FieldValue$/', $method, $matches)) {
            return $this->callFieldValueMethod(\array_shift($args), $matches[1], $args);
        }

        return parent::__call($method, $args);
    }
}
