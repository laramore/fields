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

use Illuminate\Support\Arr;
use Laramore\Elements\OptionElement;
use Laramore\Exceptions\ConfigException;
use Laramore\Facades\Option;
use Laramore\Fields\LinkField;
use Laramore\Interfaces\{
    IsAFieldOwner,
    IsProxied, IsAnOwner, IsALaramoreModel, IsARelationField
};
use Laramore\Traits\Field\HasMultipleFieldConstraints;

abstract class CompositeField extends BaseField implements IsAnOwner, IsARelationField, IsAFieldOwner
{
    use HasMultipleFieldConstraints;

    /**
     * Attribute fields managed by this composite fields.
     *
     * @var array<AttributeField>
     */
    protected $attributes = [];

    /**
     * Link fields managed by this composite fields.
     *
     * @var array<LinkField>
     */
    protected $links = [];

    /**
     * Name of each attribute fields.
     *
     * @var array<string>
     */
    protected $attributesName = [];

    /**
     * Name of each link fields.
     *
     * @var array<string>
     */
    protected $linksName = [];

    /**
     * Create a new field with basic options.
     * The constructor is protected so the field is created writing left to right.
     * ex: Foreign::field()->on(User::class) insteadof (new Foreign)->on(User::class).
     *
     * @param array $options
     * @param array $attributes Allow the user to define sub fields.
     * @param array $links      Allow the user to define sub links.
     */
    protected function __construct(array $options=null, array $attributes=null, array $links=null)
    {
        parent::__construct($options);

        $attributes = ($attributes ?: $this->getConfig('attributes'));

        if (\is_null($attributes) || (\count($attributes) && !Arr::isAssoc($attributes))) {
            throw new ConfigException($this->getConfigPath('attributes'), 'any associative array of fields', $attributes);
        }

        foreach ($attributes as $key => $attribute) {
            if (!\is_string($key)) {
                throw new \Exception('Attibute fields need names');
            }

            $this->attributes[$key] = $this->generateAttribute($attribute);
        }

        $links = ($links ?: $this->getConfig('links'));

        if (\is_null($links) || (\count($links) && !Arr::isAssoc($links))) {
            throw new ConfigException($this->getConfigPath('links'), 'any associative array of links', $links);
        }

        foreach ($links ?: $this->getConfig('links') as $key => $link) {
            if (!\is_string($key)) {
                throw new \Exception('Link fields need names');
            }

            $this->links[$key] = $this->generateLink($link);
        }
    }

    /**
     * Call the constructor and generate the field.
     *
     * @param array $options
     * @param array $attributes Allow the user to define sub fields.
     * @param array $links      Allow the user to define sub links.
     * @return self
     */
    public static function field(array $options=null, array $attributes=null, array $links=null)
    {
        return new static($options, $attributes, $links);
    }

    /**
     * Generate a field by its class name.
     *
     * @param  array|string|AttributeField $attribute
     * @return AttributeField
     */
    protected function generateAttribute($attribute): AttributeField
    {
        if (\is_array($attribute)) {
            return $attribute[0]::field($attribute[1]);
        } else if (\is_string($attribute)) {
            return $attribute::field();
        }

        return $attribute;
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
            return $link[0]::field($link[1]);
        } else if (\is_string($link)) {
            return $link::field();
        }

        return $link;
    }

    /**
     * Indicate if this composite has a field.
     *
     * @param  string $name
     * @return boolean
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->getAttributes()[$name]);
    }

    /**
     * Return the field with the given name.
     *
     * @param  string $name
     * @return AttributeField
     */
    public function getAttribute(string $name): AttributeField
    {
        if ($this->hasAttribute($name)) {
            return $this->getAttributes()[$name];
        } else {
            throw new \Exception($name.' field does not exist');
        }
    }

    /**
     * Return all sub attributes.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
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
     * @return AttributeField
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
    public function hasField(string $name)
    {
        return isset($this->getFields()[$name]);
    }

    /**
     * Return the field or link with the given name.
     *
     * @param  string $name
     * @return Field
     */
    public function getField(string $name)
    {
        if ($this->hasField($name)) {
            return $this->getFields()[$name];
        } else {
            throw new \Exception($name.' real or link field does not exist');
        }
    }

    /**
     * Return getFields sub attributes and links.
     *
     * @return array
     */
    public function getFields()
    {
        return array_merge(
	        $this->attributes,
	        $this->links
        );
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
            foreach ($this->getAttributes() as $attribute) {
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

        foreach ($this->getAttributes() as $attribute) {
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

        $this->ownAttributes();
        $this->ownLinks();
    }

    /**
     * Own each sub attributes.
     *
     * @return void
     */
    protected function ownAttributes()
    {
        foreach ($this->attributes as $attributename => $attribute) {
            $template = ($this->attributesName[$attributename] ?? $this->getConfig('attribute_name_template'));
            $name = $this->replaceInFieldTemplate($template, $attributename);

            $this->attributes[$attributename] = $attribute->own($this, $name);
        }
    }

    /**
     * Own each sub links.
     *
     * @return void
     */
    protected function ownLinks()
    {
        foreach ($this->links as $linkname => $link) {
            $template = ($this->linksName[$linkname] ?? $this->getConfig('link_name_template'));
            $name = $this->replaceInFieldTemplate($template, $linkname);

            $this->links[$linkname] = $link->own($this, $name);
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
            'fieldname' => $fieldname,
            'name' => $this->getName(),
        ];

        return static::replaceInTemplate($template, $keyValues);
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

        if ((count($this->attributes) + count($this->links)) === 0) {
            throw new \Exception('A composite field needs at least one field or link');
        }

        parent::locking();
    }

    /**
     * Lock each sub attributes.
     *
     * @return void
     */
    protected function lockFields()
    {
        foreach ($this->attributes as $attribute) {
            $attribute->lock();
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
     * Get the value definied by the field.
     *
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function get(IsALaramoreModel $model)
    {
        return $model->getRelationValue($this->getNative());
    }

    /**
     * Set the value for the field.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return mixed
     */
    public function set(IsALaramoreModel $model, $value)
    {
        return $model->setRawRelationValue($this->getNative(), $value);
    }

    /**
     * Reet the value for the field.
     *
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function reset(IsALaramoreModel $model)
    {
        return $model->setRawRelationValue($this->getNative(), $this->getProperty('default'));
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
     * z
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
     * Return the get value for a relation field.
     *
     * @param IsARelationField $attribute
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function relateFieldAttribute(IsARelationField $attribute, IsALaramoreModel $model)
    {
        return $this->getOwner()->relateFieldAttribute($attribute, $model);
    }

    /**
     * Reverbate a saved relation value for a specific field.
     *
     * @param IsARelationField $attribute
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return boolean
     */
    public function reverbateFieldAttribute(IsARelationField $attribute, IsALaramoreModel $model, $value): bool
    {
        return $this->getOwner()->reverbateFieldAttribute($attribute, $model, $value);
    }

    /**
     * Return generally a Builder after adding to it a condition.
     *
     * @param BaseField            $attribute
     * @param IsProxied            $builder
     * @param Operator|string|null $operator
     * @param mixed                $value
     * @param mixed                ...$args
     * @return mixed
     */
    public function whereFieldAttribute(BaseField $attribute, IsProxied $builder, $operator=null, $value=null, ...$args)
    {
        if (func_num_args() === 3) {
            return $this->getOwner()->whereFieldAttribute($attribute, $builder, $operator);
        }

        return $this->getOwner()->whereFieldAttribute($attribute, $builder, $operator, $value, ...$args);
    }

    /**
     * Transform a value for a specific field.
     *
     * @param BaseField $attribute
     * @param mixed     $value
     * @return mixed
     */
    public function transformFieldAttribute(BaseField $attribute, $value)
    {
        return $this->getOwner()->transformFieldAttribute($attribute, $value);
    }

    /**
     * Serialize a value for a specific field.
     *
     * @param BaseField $attribute
     * @param mixed     $value
     * @return mixed
     */
    public function serializeFieldAttribute(BaseField $attribute, $value)
    {
        return $this->getOwner()->serializeFieldAttribute($attribute, $value);
    }

    /**
     * Check if the value is correct for a specific field.
     *
     * @param BaseField $attribute
     * @param mixed     $value
     * @return mixed
     */
    public function checkFieldAttribute(BaseField $attribute, $value)
    {
        return $this->getOwner()->checkFieldAttribute($attribute, $value);
    }

    /**
     * Dry a value for a specific field.
     *
     * @param BaseField $attribute
     * @param mixed     $value
     * @return mixed
     */
    public function dryFieldAttribute(BaseField $attribute, $value)
    {
        return $this->getOwner()->dryFieldAttribute($attribute, $value);
    }

    /**
     * Cast a value for a specific field.
     *
     * @param BaseField $attribute
     * @param mixed     $value
     * @return mixed
     */
    public function castFieldAttribute(BaseField $attribute, $value)
    {
        return $this->getOwner()->castFieldAttribute($attribute, $value);
    }

    /**
     * Return the default value for a specific field.
     *
     * @param BaseField $attribute
     * @return mixed
     */
    public function defaultFieldAttribute(BaseField $attribute)
    {
        return $this->getOwner()->defaultFieldAttribute($attribute);
    }

    /**
     * Call a field attribute method that is not basic.
     *
     * @param  BaseField $attribute
     * @param  string    $methodName
     * @param  array     $args
     * @return mixed
     */
    public function callFieldAttributeMethod(BaseField $attribute, string $methodName, array $args)
    {
        return $this->getOwner()->callFieldAttributeMethod($attribute, $methodName, $args);
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
