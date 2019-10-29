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

use Illuminate\Support\Str;
use Laramore\Fields\LinkField;
use Laramore\Interfaces\{
    IsProxied, IsAFieldOwner, IsALaramoreModel, IsARelationField
};
use Laramore\Meta;

abstract class CompositeField extends BaseField implements IsAFieldOwner, IsARelationField
{
    protected $fields = [];
    protected $links = [];
    protected $fieldsName = [];
    protected $linksName = [];
    protected $uniques = [];

    protected static $defaultFieldNameTemplate = '${name}_${fieldname}';
    protected static $defaultLinkNameTemplate = '*{modelname}';

    // Default rules for this type of field.
    public const DEFAULT_COMPOSITE = (self::DEFAULT_FIELD ^ self::REQUIRED);

    protected static $defaultRules = self::DEFAULT_COMPOSITE;

    /**
     * Create a new field with basic rules.
     * The constructor is protected so the field is created writing left to right.
     * ex: Foreign::field()->on(User::class) insteadof (new Foreign)->on(User::class).
     *
     * @param integer|string|array $rules
     * @param array                $fields Allow the user to define sub fields.
     * @param array                $links  Allow the user to define sub links.
     */
    protected function __construct($rules=null, array $fields=null, array $links=null)
    {
        parent::__construct($rules);

        foreach ($fields ?: $this->defaultFields as $key => $field) {
            if (!\is_string($key)) {
                throw new \Exception('The composite fields need names');
            }

            $this->fields[$key] = $this->generateField($field);
        }

        foreach ($links ?: $this->defaultLinks as $key => $link) {
            if (!\is_string($key)) {
                throw new \Exception('The composite fields need names');
            }

            $this->links[$key] = $this->generateLink($link);
        }
    }

    /**
     * Call the constructor and generate the field.
     *
     * @param  array|integer|null $rules
     * @param array              $fields Allow the user to define sub fields.
     * @param array              $links  Allow the user to define sub links.
     * @return static
     */
    public static function field($rules=null, array $fields=null, array $links=null)
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
     * Define the default field name template.
     *
     * @param string $defaultFieldNameTemplate
     * @return static
     */
    public static function setDefaultFieldNameTemplate(string $defaultFieldNameTemplate)
    {
        static::$defaultFieldNameTemplate = $defaultFieldNameTemplate;

        return static::class;
    }

    /**
     * Define the default link name template.
     *
     * @param string $defaultLinkNameTemplate
     * @return static
     */
    public static function setDefaultLinkNameTemplate(string $defaultLinkNameTemplate)
    {
        static::$defaultLinkNameTemplate = $defaultLinkNameTemplate;

        return static::class;
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
     * Remplace in template with values.
     *
     * @param  string $template
     * @param  array  $keyValues
     * @return string
     */
    protected function replaceInTemplate(string $template, array $keyValues): string
    {
        foreach ($keyValues as $varName => $value) {
            $template = \str_replace('*{'.$varName.'}', Str::plural($value),
                \str_replace('^{'.$varName.'}', \ucwords($value),
                    \str_replace('${'.$varName.'}', $value, $template)
                )
            );
        }

        return $template;
    }

    /**
     * Add a rule to the resource.
     *
     * @param integer $rule
     * @return self
     */
    protected function addRule(int $rule)
    {
        foreach ($this->all() as $field) {
            $field->addRule($rule);
        }

        return parent::addRule($rule);
    }

    /**
     * Remove a rule from the resource.
     *
     * @param  integer $rule
     * @return self
     */
    protected function removeRule(int $rule)
    {
        foreach ($this->all() as $field) {
            $field->removeRule($rule);
        }

        return parent::removeRule($rule);
    }

    /**
     * Define this composite as unique.
     *
     * @return self
     */
    public function unique()
    {
        $this->needsToBeUnlocked();

        $this->unique[] = $this->getFields();

        return $this;
    }

    /**
     * Return the unique fields.
     *
     * @return array
     */
    public function getUnique(): array
    {
        return $this->unique;
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
            $name = $this->replaceInTemplate(($this->fieldsName[$fieldname] ?? static::$defaultFieldNameTemplate), $keyValues);
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
            $name = $this->replaceInTemplate(($this->linksName[$linkname] ?? static::$defaultLinkNameTemplate), $keyValues);
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
        if (\preg_match('/^(.*)FieldAttribute$/', $method, $matches)) {
            return $this->callFieldAttributeMethod(\array_shift($args), $matches[1], $args);
        }

        return parent::__call($method, $args);
    }
}
