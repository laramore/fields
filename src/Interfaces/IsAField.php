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
use Laramore\Eloquent\{
    Model, Builder
};
use Laramore\Interfaces\{
    IsProxied, IsLockable, IsOwnable
};
use Laramore\Elements\Operator;

interface IsAField extends IsLockable, IsOwnable
{
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
     * @return static
     */
    public function setProperty(string $key, $value);

    /**
     * Handle all calls to define field properies.
     *
     * @param  string $method
     * @param  array  $args
     * @return static
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
     * Cast the value in the correct format.
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
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value);

    /**
     * Serialize the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value);

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function check($value);

    /**
     * Add a where condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @param  boolean $not
     * @return Builder|void
     */
    public function whereNull(Builder $builder, $value=null, string $boolean='and', bool $not=false);

    /**
     * Add a where condition from this field.
     *
     * @param  Builder $builder
     * @param  mixed   $value
     * @param  string  $boolean
     * @return Builder|void
     */
    public function whereNotNull(Builder $builder, $value=null, string $boolean='and');

    /**
     * Add a where condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @param  boolean    $notIn
     * @return Builder|void
     */
    public function whereIn(Builder $builder, Collection $value=null, string $boolean='and', bool $notIn=false);

    /**
     * Add a where condition from this field.
     *
     * @param  Builder    $builder
     * @param  Collection $value
     * @param  string     $boolean
     * @return Builder|void
     */
    public function whereNotIn(Builder $builder, Collection $value=null, string $boolean='and');

    /**
     * Add a where condition from this field.
     *
     * @param  Builder  $builder
     * @param  Operator $operator
     * @param  mixed    $value
     * @param  string   $boolean
     * @return Builder|void
     */
    public function where(Builder $builder, Operator $operator, $value=null, string $boolean='and');
}
