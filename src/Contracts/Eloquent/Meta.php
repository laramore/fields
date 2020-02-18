<?php
/**
 * Laramore meta.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Eloquent;

use Laramore\Contracts\Field\FieldsOwner;
use Laramore\Proxies\ProxyHandler;
use Laramore\Fields\Constraint\ConstraintHandler;

interface Meta extends FieldsOwner
{
    /**
     * Return the table name.
     *
     * @return string
     */
    public function getTableName(): string;

    /**
     * Define the table name.
     *
     * @param string $tableName
     * @return self
     */
    public function setTableName(string $tableName);

    /**
     * Return the model class.
     *
     * @return string
     */
    public function getModelClass(): string;

    /**
     * Get the model short name.
     *
     * @return string|null
     */
    public function getModelClassName(): mixed;

    /**
     * Return the proxy handler for this meta.
     *
     * @return ProxyHandler
     */
    public function getProxyHandler(): ProxyHandler;

    /**
     * Return the relation handler for this meta.
     *
     * @return ConstraintHandler
     */
    public function getConstraintHandler(): ConstraintHandler;

    /**
     * Define a primary constraint.
     *
     * @param  string|array $fields
     * @param  string       $name
     * @return self
     */
    public function primary($fields, string $name=null);

    /**
     * Define an index constraint.
     *
     * @param  string|array $fields
     * @param  string       $name
     * @return self
     */
    public function index($fields, string $name=null);

    /**
     * Define an unique constraint.
     *
     * @param  string|array $fields
     * @param  string       $name
     * @return self
     */
    public function unique($fields, string $name=null);

    /**
     * Define an foreign constraint.
     *
     * @param  string|array $fields
     * @param  string       $name
     * @return self
     */
    public function foreign($fields, string $name=null);

    /**
     * Return the only one primary constraint.
     *
     * @return Primary|null
     */
    public function getPrimary();

    /**
     * Return all index constraints.
     *
     * @return array
     */
    public function getIndexes(): array;

    /**
     * Return all unique constraints.
     *
     * @return array
     */
    public function getUniques(): array;

    /**
     * Return all foreign constraints.
     *
     * @return array
     */
    public function getForeigns(): array;
}
