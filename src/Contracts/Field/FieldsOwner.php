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

use Laramore\Contracts\{
    Proxied, Eloquent\LaramoreModel, Field\Field, Field\RelationField, Field\ExtraField
};

interface FieldsOwner
{
    /**
     * Indicate if a field with a given name exists.
     *
     * @param  string $name
     * @param  string $class The field must be an instance of the class.
     * @return boolean
     */
    public function hasField(string $name, string $class=null): bool;

    /**
     * Return a field with a given name.
     *
     * @param  string $name
     * @param  string $class The field must be an instance of the class.
     * @return Field
     */
    public function getField(string $name, string $class=null): Field;

    /**
     * Define a field with a given name.
     *
     * @param string $name
     * @param Field  $field
     * @return self
     */
    public function setField(string $name, Field $field);

    /**
     * Return all fields.
     *
     * @param  string $class Each field must be an instance of the class.
     * @return array
     */
    public function getFields(string $class=null): array;

    /**
     * Return the get value for a specific field.
     *
     * @param Field         $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function getFieldValue(Field $field, LaramoreModel $model);

    /**
     * Return the set value for a specific field.
     * z
     * @param Field         $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return mixed
     */
    public function setFieldValue(Field $field, LaramoreModel $model, $value);

    /**
     * Reset the value with the default value for a specific field.
     *
     * @param Field         $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function resetFieldValue(Field $field, LaramoreModel $model);

    /**
     * Return the get value for a relation field.
     *
     * @param RelationField $field
     * @param LaramoreModel $model
     * @return mixed
     */
    public function relateFieldValue(RelationField $field, LaramoreModel $model);

    /**
     * Retrieve values from the relation field.
     *
     * @param ExtraField $field
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function retrieveFieldValue(ExtraField $field, LaramoreModel $model);

    /**
     * Reverbate a saved relation value for a specific field.
     *
     * @param RelationField $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return boolean
     */
    public function reverbateFieldValue(RelationField $field, LaramoreModel $model, $value): bool;

    /**
     * Return generally a Builder after adding to it a condition.
     *
     * @param Field                       $field
     * @param Proxied                     $builder
     * @param OperatorElement|string|null $operator
     * @param mixed                       $value
     * @param mixed                       ...$args
     * @return mixed
     */
    public function whereFieldValue(Field $field, Proxied $builder, $operator=null, $value=null, ...$args);

    /**
     * Transform a value for a specific field.
     *
     * @param Field $field
     * @param mixed $value
     * @return mixed
     */
    public function transformFieldValue(Field $field, $value);

    /**
     * Serialize a value for a specific field.
     *
     * @param Field $field
     * @param mixed $value
     * @return mixed
     */
    public function serializeFieldValue(Field $field, $value);

    /**
     * Dry a value for a specific field.
     *
     * @param Field $field
     * @param mixed $value
     * @return mixed
     */
    public function dryFieldValue(Field $field, $value);

    /**
     * Cast a value for a specific field.
     *
     * @param Field $field
     * @param mixed $value
     * @return mixed
     */
    public function castFieldValue(Field $field, $value);

    /**
     * Return the default value for a specific field.
     *
     * @param Field $field
     * @return mixed
     */
    public function defaultFieldValue(Field $field);

    /**
     * Call a field attribute method that is not basic.
     *
     * @param Field  $field
     * @param string $methodName
     * @param array  $args
     * @return mixed
     */
    public function callFieldValueMethod(Field $field, string $methodName, array $args);
}
