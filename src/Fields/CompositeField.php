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
use Laramore\Elements\Rule;
use Laramore\Exceptions\ConfigException;
use Laramore\Facades\Rules;
use Laramore\Fields\LinkField;
use Laramore\Interfaces\{
    IsProxied, IsAFieldOwner, IsALaramoreModel, IsARelationField
};
use Laramore\Traits\Field\HasMultipleFieldConstraints;

abstract class CompositeField extends BaseField implements IsAFieldOwner, IsARelationField
{
    use HasMultipleFieldConstraints;

    /**
     * Attribute fields managed by this composite fields.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Link fields managed by this composite fields.
     *
     * @var array
     */
    protected $links = [];

    /**
     * Name of each attribute fields.
     *
     * @var array
     */
    protected $attributesName = [];

    /**
     * Name of each link fields.
     *
     * @var array
     */
    protected $linksName = [];

    /**
     * Create a new field with basic rules.
     * The constructor is protected so the field is created writing left to right.
     * ex: Foreign::field()->on(User::class) insteadof (new Foreign)->on(User::class).
     *
     * @param array $rules
     * @param array $attributes Allow the user to define sub fields.
     * @param array $links      Allow the user to define sub links.
     */
    protected function __construct(array $rules=null, array $attributes=null, array $links=null)
    {
        parent::__construct($rules);

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
     * @param array $rules
     * @param array $attributes Allow the user to define sub fields.
     * @param array $links      Allow the user to define sub links.
     * @return static
     */
    public static function field(array $rules=null, array $attributes=null, array $links=null)
    {
        return new static($rules, $attributes, $links);
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
            return \array_shift($attribute)::field(...$attribute);
        } else if (\is_string($attribute)) {
            return $attribute::field();
        } else {
            return $attribute;
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

        foreach ($this->getAttributes() as $attribute) {
            $attribute->addRule($rule);
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

        foreach ($this->getAttributes() as $attribute) {
            $attribute->removeRule($rule);
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
            $name = $this->replaceInFieldTemplate(($this->attributesName[$attributename] ?? $this->getConfig('attribute_name_template')), $attributename);
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
            $name = $this->replaceInFieldTemplate(($this->linksName[$linkname] ?? $this->getConfig('link_name_template')), $linkname);
            $this->links[$linkname] = $link->own($this, $name);
        }
    }

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
     * Return the get value for a specific field.
     *
     * @param BaseField        $attribute
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function getValueFieldAttribute(BaseField $attribute, IsALaramoreModel $model)
    {
        return $this->getOwner()->getValueFieldAttribute($attribute, $model);
    }

    /**
     * Return the set value for a specific field.
     *
     * @param BaseField        $attribute
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return mixed
     */
    public function setValueFieldAttribute(BaseField $attribute, IsALaramoreModel $model, $value)
    {
        return $this->getOwner()->setValueFieldAttribute($attribute, $model, $value);
    }

    /**
     * Reset the value with the default value for a specific field.
     *
     * @param BaseField        $attribute
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function resetValueFieldAttribute(BaseField $attribute, IsALaramoreModel $model)
    {
        return $this->getOwner()->resetValueFieldAttribute($attribute, $model);
    }

    /**
     * Return the get value for a relation field.
     *
     * @param IsARelationField $attribute
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function getRelationFieldAttribute(IsARelationField $attribute, IsALaramoreModel $model)
    {
        return $this->getOwner()->getRelationFieldAttribute($attribute, $model);
    }

    /**
     * Return the set value for a relation field.
     *
     * @param IsARelationField $attribute
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return mixed
     */
    public function setRelationFieldAttribute(IsARelationField $attribute, IsALaramoreModel $model, $value)
    {
        return $this->getOwner()->setRelationFieldAttribute($attribute, $model, $value);
    }

    /**
     * Reverbate a saved relation value for a specific field.
     *
     * @param IsARelationField $attribute
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return boolean
     */
    public function reverbateRelationFieldAttribute(IsARelationField $attribute, IsALaramoreModel $model, $value): bool
    {
        return $this->getOwner()->reverbateRelationFieldAttribute($attribute, $model, $value);
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
     * Return the query with this field as condition.
     *
     * @param BaseField $attribute
     * @param IsProxied $model
     * @return mixed
     */
    public function relateFieldAttribute(BaseField $attribute, IsProxied $model)
    {
        return $this->getOwner()->relateFieldAttribute($attribute, $model);
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
