<?php
/**
 * Laramore model.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Eloquent;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Queue\QueueableEntity;
use Laramore\Contracts\Proxied;

interface LaramoreModel extends Proxied, ArrayAccess, Arrayable, Jsonable, JsonSerializable, QueueableEntity, UrlRoutable
{
    /**
     * Generate one time the model meta.
     *
     * @return void
     */
    public static function generateMeta();

    /**
     * Return the meta class to use.
     *
     * @return string
     */
    public static function getMetaClass(): string;

    /**
     * Get the model meta.
     *
     * @return Meta
     */
    public static function getMeta();

    /**
     * Reset a specific field.
     *
     * @param  string $key Name of the field.
     * @return $this
     */
    public function resetAttribute(string $key);

    /**
     * Get an attribute from the model.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getAttribute($key);

    /**
     * Set a given attribute on the model.
     * Override the original method.
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return mixed
     *
     * @throws Exception Except if the field is not fillable.
     */
    public function setAttribute($key, $value);

    /**
     * Get a plain attribute (not a relationship).
     * Override the original method.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getAttributeValue($key);

    /**
     * Get the relation value for a specific key.
     *
     * @param  mixed $key Not specified because Model has no parameter types.
     * @return mixed
     */
    public function getRelation($key);

    /**
     * Get a relationship.
     * Override the original method.
     *
     * @param  mixed $key
     * @return mixed
     */
    public function getRelationValue($key);

    /**
     * Set the given relationship on the model.
     *
     * @param  mixed $key
     * @param  mixed $value
     * @return $this
     */
    public function setRelationValue($key, $value);
}
