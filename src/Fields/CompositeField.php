<?php
/**
 * Define a composite field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\{
	Arr, Str
};
use Laramore\Elements\Rule;
use Laramore\Exceptions\ConfigException;
use Laramore\Facades\Rules;
use Laramore\Fields\LinkField;
use Laramore\Interfaces\{
    IsProxied, IsAFieldOwner, IsALaramoreModel, IsARelationField
};
use Laramore\Traits\Field\HasMultipleFieldConstraints;
use Laramore\Meta;

abstract class CompositeField extends BaseField implements IsAFieldOwner, IsARelationField
{
    use HasMultipleFieldConstraints;

    protected $fields = [];
    protected $links = [];
    protected $fieldsName = [];
    protected $linksName = [];

    /**
     * Create a new field with basic rules.
     * The constructor is protected so the field is created writing left to right.
     * ex: Foreign::field()->on(User::class) insteadof (new Foreign)->on(User::class).
     *
     * @param array $rules
     * @param array $fields Allow the user to define sub fields.
     * @param array $links  Allow the user to define sub links.
     */
    protected function __construct(array $rules=null, array $fields=null, array $links=null)
    {
        parent::__construct($rules);

        $fields = ($fields ?: $this->getConfig('fields'));

        if (\is_null($fields) || (\count($fields) && !Arr::isAssoc($fields))) {
            throw new ConfigException($this->getConfigPath('fields'), 'any associative array of fields', $fields);
        }

        foreach ($fields as $key => $field) {
            if (!\is_string($key)) {
                throw new \Exception('The composite fields need names');
            }

            $this->fields[$key] = $this->generateField($field);
        }

        $links = ($links ?: $this->getConfig('links'));

        if (\is_null($links) || (\count($links) && !Arr::isAssoc($links))) {
            throw new ConfigException($this->getConfigPath('links'), 'any associative array of links', $links);
        }

        foreach ($links ?: $this->getConfig('links') as $key => $link) {
            if (!\is_string($key)) {
                throw new \Exception('The composite fields need names');
            }

            $this->links[$key] = $this->generateLink($link);
        }
    }

    /**
     * Call the constructor and generate the field.
     *
     * @param array $rules
     * @param array $fields Allow the user to define sub fields.
     * @param array $links  Allow the user to define sub links.
     * @return static
     */
    public static function field(array $rules=null, array $fields=null, array $links=null)
    {
        return new static($rules, $fields, $links);
    }

    /**
     * Generate a field by its class name.
     *
     * @param  array|string|Field $field
     * @return Field
     */
    protected function generateField($field): Field
    {
        if (\is_array($field)) {
            return \array_shift($field)::field(...$field);
        } else if (\is_string($field)) {
            return $field::field();
        } else {
            return $field;
        }
    }

    /**
     * Generate a link by its class name.
     *
     * @param  array|string|LinkField $link
     * @return LinkField
     */
    protected function generateLink($link): LinkField
    {
        if (\is_array($link)) {
            return \array_shift($link)::field(...$link);
        } else if (\is_string($link)) {
            return $link::field();
        } else {
            return $link;
        }
    }

    /**
     * Indicate if this composite has a field.
     *
     * @param  string $name
     * @return boolean
     */
    public function hasField(string $name): bool
    {
        return isset($this->getFields()[$name]);
    }

    /**
     * Return the field with the given name.
     *
     * @param  string $name
     * @return Field
     */
    public function getField(string $name): Field
    {
        if ($this->hasField($name)) {
            return $this->getFields()[$name];
        } else {
            throw new \Exception($name.' field does not exist');
        }
    }

    /**
     * Return all sub fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Indicate if this composite has a link.
     *
     * @param  string $name
     * @return boolean
     */
    public function hasLink(string $name)
    {
        return isset($this->getLinks()[$name]);
    }

    /**
     * Return the link with the given name.
     *
     * @param  string $name
     * @return Field
     */
    public function getLink(string $name)
    {
        if ($this->hasLink($name)) {
            return $this->getLinks()[$name];
        } else {
            throw new \Exception($name.' link field does not exist');
        }
    }

    /**
     * Return all sub links.
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Indicate if this composite has a field or a link.
     *
     * @param  string $name
     * @return boolean
     */
    public function has(string $name)
    {
        return isset($this->all()[$name]);
    }

    /**
     * Return the field or link with the given name.
     *
     * @param  string $name
     * @return Field
     */
    public function get(string $name)
    {
        if ($this->has($name)) {
            return $this->all()[$name];
        } else {
            throw new \Exception($name.' real or link field does not exist');
        }
    }

    /**
     * Return all sub fields and links.
     *
     * @return array
     */
    public function all()
    {
        return array_merge(
	        $this->fields,
	        $this->links
        );
    }

    /**
     * Add a rule to the resource.
     *
     * @param string|Rule $rule
     * @return self
     */
    protected function addRule($rule)
    {
        if (\is_string($rule)) {
            $rule = Rules::get($rule);
        }

        return parent::addRule($rule);
    }

    /**
     * Remove a rule from the resource.
     *
     * @param  string|Rule $rule
     * @return self
     */
    protected function removeRule($rule)
    {
        if (\is_string($rule)) {
            $rule = Rules::get($rule);
        }

        return parent::removeRule($rule);
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
        $this->ownLinks();
    }

    /**
     * Own each sub fields.
     *
     * @return void
     */
    protected function ownFields()
    {
        $keyValues = [
            'modelname' => strtolower($this->getMeta()->getModelClassName()),
            'name' => $this->name,
        ];

        foreach ($this->fields as $fieldname => $field) {
            $keyValues['fieldname'] = $fieldname;
            $template = ($this->fieldsName[$fieldname] ?? $this->getConfig('field_name_template'));
            $name = $this->replaceInTemplate($template, $keyValues);
            $this->fields[$fieldname] = $field->own($this, $name);
        }
    }

    /**
     * Own each sub links.
     *
     * @return void
     */
    protected function ownLinks()
    {
        $keyValues = [
            'modelname' => strtolower($this->getMeta()->getModelClassName()),
            'name' => $this->name,
        ];

        foreach ($this->links as $linkname => $link) {
            $keyValues['linkname'] = $linkname;
            $template = ($this->linksName[$linkname] ?? $this->getConfig('link_name_template'));
            $name = $this->replaceInTemplate($template, $keyValues);
            $this->links[$linkname] = $link->own($this, $name);
        }
    }

    /**
     * Each class locks in a specific way.
     *
     * @return void
     */
    protected function locking()
    {
        $this->setConstraints();

        $this->lockFields();
        $this->lockLinks();

        if ((count($this->fields) + count($this->links)) === 0) {
            throw new \Exception('A composite field needs at least one field or link');
        }

        parent::locking();
    }

    /**
     * Lock each sub fields.
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
     * Lock each sub links.
     *
     * @return void
     */
    protected function lockLinks()
    {
        foreach ($this->links as $link) {
            $link->lock();
        }
    }

    /**
     * Return the get value for a specific field.
     *
     * @param BaseField        $field
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function getFieldAttribute(BaseField $field, IsALaramoreModel $model)
    {
        return $this->getOwner()->getFieldAttribute($field, $model);
    }

    /**
     * Return the set value for a specific field.
     *
     * @param BaseField        $field
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return mixed
     */
    public function setFieldAttribute(BaseField $field, IsALaramoreModel $model, $value)
    {
        return $this->getOwner()->setFieldAttribute($field, $model, $value);
    }

    /**
     * Return the get value for a relation field.
     *
     * @param IsARelationField $field
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function getRelationFieldAttribute(IsARelationField $field, IsALaramoreModel $model)
    {
        return $this->getOwner()->getRelationFieldAttribute($field, $model);
    }

    /**
     * Return the set value for a relation field.
     *
     * @param IsARelationField $field
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return mixed
     */
    public function setRelationFieldAttribute(IsARelationField $field, IsALaramoreModel $model, $value)
    {
        return $this->getOwner()->setRelationFieldAttribute($field, $model, $value);
    }

    /**
     * Reverbate a saved relation value for a specific field.
     *
     * @param IsARelationField $field
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return boolean
     */
    public function reverbateRelationFieldAttribute(IsARelationField $field, IsALaramoreModel $model, $value): bool
    {
        return $this->getOwner()->reverbateRelationFieldAttribute($field, $model, $value);
    }

    /**
     * Return generally a Builder after adding to it a condition.
     *
     * @param BaseField            $field
     * @param IsProxied            $builder
     * @param Operator|string|null $operator
     * @param mixed                $value
     * @param mixed                ...$args
     * @return mixed
     */
    public function whereFieldAttribute(BaseField $field, IsProxied $builder, $operator=null, $value=null, ...$args)
    {
        if (func_num_args() === 3) {
            return $this->getOwner()->whereFieldAttribute($field, $builder, $operator);
        }

        return $this->getOwner()->whereFieldAttribute($field, $builder, $operator, $value, ...$args);
    }

    /**
     * Return the query with this field as condition.
     *
     * @param BaseField $field
     * @param IsProxied $model
     * @return mixed
     */
    public function relateFieldAttribute(BaseField $field, IsProxied $model)
    {
        return $this->getOwner()->relateFieldAttribute($field, $model);
    }

    /**
     * Reset the value with the default value for a specific field.
     *
     * @param BaseField        $field
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function resetFieldAttribute(BaseField $field, IsALaramoreModel $model)
    {
        return $this->getOwner()->resetFieldAttribute($field, $model);
    }

    /**
     * Transform a value for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function transformFieldAttribute(BaseField $field, $value)
    {
        return $this->getOwner()->transformFieldAttribute($field, $value);
    }

    /**
     * Serialize a value for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function serializeFieldAttribute(BaseField $field, $value)
    {
        return $this->getOwner()->serializeFieldAttribute($field, $value);
    }

    /**
     * Check if the value is correct for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function checkFieldAttribute(BaseField $field, $value)
    {
        return $this->getOwner()->checkFieldAttribute($field, $value);
    }

    /**
     * Dry a value for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function dryFieldAttribute(BaseField $field, $value)
    {
        return $this->getOwner()->dryFieldAttribute($field, $value);
    }

    /**
     * Cast a value for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function castFieldAttribute(BaseField $field, $value)
    {
        return $this->getOwner()->castFieldAttribute($field, $value);
    }

    /**
     * Return the default value for a specific field.
     *
     * @param BaseField $field
     * @return mixed
     */
    public function defaultFieldAttribute(BaseField $field)
    {
        return $this->getOwner()->defaultFieldAttribute($field);
    }

    /**
     * Call a field attribute method that is not basic.
     *
     * @param  BaseField $field
     * @param  string    $methodName
     * @param  array     $args
     * @return mixed
     */
    public function callFieldAttributeMethod(BaseField $field, string $methodName, array $args)
    {
        return $this->getOwner()->callFieldAttributeMethod($field, $methodName, $args);
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

        if (\preg_match('/^(.*)FieldAttribute$/', $method, $matches)) {
            return $this->callFieldAttributeMethod(\array_shift($args), $matches[1], $args);
        }

        return parent::__call($method, $args);
    }
}
