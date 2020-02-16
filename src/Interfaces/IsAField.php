<?php
/**
 * Field interface.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Interfaces;

use Illuminate\Support\Collection;
use Laramore\Eloquent\Builder;
use Laramore\Interfaces\{
    IsLockable, IsOwnable
};
use Laramore\Elements\{
    TypeElement, OperatorElement
};

interface IsAField extends IsLockable, IsOwnable
{
    /**
     * Return the type object of the field.
     *
     * @return TypeElement
     */
    public function getType(): TypeElement;

     /**
      * Return the native value of this field.
      * Commonly, its name.
      *
      * @return string
      */
    public function getNative(): string;

    /**
     * Return the fully qualified name.
     *
     * @return string
     */
    public function getFullName(): string;

    /**
     * Indicate if a propery exists.
     *
     * @param  string $key
     * @return boolean
     */
    public function hasProperty(string $key): bool;

    /**
     * Return a property value.
     *
     * @param  string $key
     * @return mixed
     */
    public function getProperty(string $key);

    /**
     * Define a property value.
     *
     * @param string $key
     * @param mixed  $value
     * @return self
     */
    public function setProperty(string $key, $value);

    /**
     * Handle all calls to define field properies.
     *
     * @param  string $method
     * @param  array  $args
     * @return self
     */
    public function __call(string $method, array $args);

    /**
     * Return a property value.
     *
     * @param  string $key
     * @return mixed
     */
    public function __get(string $key);

    /**
     * Set a property value.
     *
     * @param  string $key
     * @param  mixed  $attvalue
     * @return mixed
     */
    public function __set(string $key, $attvalue);

    /**
     * Indicate if a property exists.
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset(string $key): bool;

    /**
     * Dry the value in a simple format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function dry($value);

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast($value);

    /**
     * Transform the value to be used as a correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value);

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value);

    /**
     * Get the value definied by the field.
     *
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function get(IsALaramoreModel $model);

    /**
     * Set the value for the field.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return mixed
     */
    public function set(IsALaramoreModel $model, $value);

    /**
     * Reset the value for the field.
     *
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function reset(IsALaramoreModel $model);

    /**
     * Add a where null condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @param  boolean $not
     * @return Builder
     */
    public function whereNull(Builder $builder, $value=null, string $boolean='and', bool $not=false): Builder;

    /**
     * Add a where not null condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @return Builder
     */
    public function whereNotNull(Builder $builder, $value=null, string $boolean='and'): Builder;

    /**
     * Add a where in condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @param  boolean    $notIn
     * @return Builder
     */
    public function whereIn(Builder $builder, Collection $value=null, string $boolean='and', bool $notIn=false): Builder;

    /**
     * Add a where not in condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @return Builder
     */
    public function whereNotIn(Builder $builder, Collection $value=null, string $boolean='and'): Builder;

    /**
     * Add a where condition from this field.
     *
     * @param  Builder         $builder
     * @param  OperatorElement $operator
     * @param  mixed           $value
     * @param  string          $boolean
     * @return Builder
     */
    public function where(Builder $builder, OperatorElement $operator, $value=null, string $boolean='and'): Builder;
}
