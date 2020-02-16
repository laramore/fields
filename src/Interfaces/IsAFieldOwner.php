<?php
/**
 * Owner interface.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Interfaces;

use Laramore\Fields\BaseField;
use Laramore\Interfaces\{
    IsProxied, IsALaramoreModel, IsARelationField
};

interface IsAFieldOwner extends IsAnOwner
{
    /**
     * Return the get value for a specific field.
     *
     * @param BaseField        $field
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function getFieldAttribute(BaseField $field, IsALaramoreModel $model);

    /**
     * Return the set value for a specific field.
     * z
     * @param BaseField        $field
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return mixed
     */
    public function setFieldAttribute(BaseField $field, IsALaramoreModel $model, $value);

    /**
     * Reset the value with the default value for a specific field.
     *
     * @param BaseField        $field
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function resetFieldAttribute(BaseField $field, IsALaramoreModel $model);

    /**
     * Return the get value for a relation field.
     *
     * @param IsARelationField $field
     * @param IsALaramoreModel $model
     * @return mixed
     */
    public function relateFieldAttribute(IsARelationField $field, IsALaramoreModel $model);

    /**
     * Reverbate a saved relation value for a specific field.
     *
     * @param IsARelationField $field
     * @param IsALaramoreModel $model
     * @param mixed            $value
     * @return boolean
     */
    public function reverbateFieldAttribute(IsARelationField $field, IsALaramoreModel $model, $value): bool;

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
    public function whereFieldAttribute(BaseField $field, IsProxied $builder, $operator=null, $value=null, ...$args);

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
