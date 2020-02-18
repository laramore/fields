<?php
/**
 * Fields owner contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Fields\BaseField;
use Laramore\Contracts\{
    Proxied, Eloquent\LaramoreModel, Field\RelationField
};

interface FieldsOwner
{
    /**
     * Indicate if this meta has a classic, link or composite field with a given name.
     *
     * @param  string $name
     * @return BaseField
     */
    public function getField(string $name): BaseField;

    /**
     * Define a classic, link or composite field with a given name.
     *
     * @param string    $name
     * @param BaseField $field
     * @return self
     */
    public function setField(string $name, BaseField $field);

    /**
     * Return all attribute, link and composite fields.
     *
     * @return array
     */
    public function getFields(): array;

    /**
     * Return the get value for a specific field.
     *
     * @param BaseField     $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function getFieldAttribute(BaseField $field, LaramoreModel $model);

    /**
     * Return the set value for a specific field.
     * z
     * @param BaseField     $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return mixed
     */
    public function setFieldAttribute(BaseField $field, LaramoreModel $model, $value);

    /**
     * Reset the value with the default value for a specific field.
     *
     * @param BaseField     $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function resetFieldAttribute(BaseField $field, LaramoreModel $model);

    /**
     * Return the get value for a relation field.
     *
     * @param RelationField $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function relateFieldAttribute(RelationField $field, LaramoreModel $model);

    /**
     * Reverbate a saved relation value for a specific field.
     *
     * @param RelationField $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return boolean
     */
    public function reverbateFieldAttribute(RelationField $field, LaramoreModel $model, $value): bool;

    /**
     * Return generally a Builder after adding to it a condition.
     *
     * @param BaseField                   $field
     * @param Proxied                     $builder
     * @param OperatorElement|string|null $operator
     * @param mixed                       $value
     * @param mixed                       ...$args
     * @return mixed
     */
    public function whereFieldAttribute(BaseField $field, Proxied $builder, $operator=null, $value=null, ...$args);

    /**
     * Transform a value for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function transformFieldAttribute(BaseField $field, $value);

    /**
     * Serialize a value for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function serializeFieldAttribute(BaseField $field, $value);

    /**
     * Dry a value for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function dryFieldAttribute(BaseField $field, $value);

    /**
     * Cast a value for a specific field.
     *
     * @param BaseField $field
     * @param mixed     $value
     * @return mixed
     */
    public function castFieldAttribute(BaseField $field, $value);

    /**
     * Return the default value for a specific field.
     *
     * @param BaseField $field
     * @return mixed
     */
    public function defaultFieldAttribute(BaseField $field);

    /**
     * Call a field attribute method that is not basic.
     *
     * @param BaseField $field
     * @param string    $methodName
     * @param array     $args
     * @return mixed
     */
    public function callFieldAttributeMethod(BaseField $field, string $methodName, array $args);
}
