<?php
/**
 * Relation field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Eloquent\LaramoreModel;
use Laramore\Contracts\Field\{
    AttributeField, Constraint\Constraint
};

interface RelationField extends ExtraField
{
    /**
     * Return the relation with this field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function relate(LaramoreModel $model);

    /**
     * Reverbate the relation into database or other fields.
     * It should be called by the set method.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function reverbate(LaramoreModel $model, $value);

    /**
     * Indicate if the relation is head on or not.
     * Usefull to know which to use between source and target.
     *
     * @return boolean
     */
    public function isRelationHeadOn(): bool;

    /**
     * Model where the relation is set from.
     *
     * @return string
     */
    public function getSourceModel(): string;

    /**
     * Return the main attribute where to start the relation from.
     *
     * @return AttributeField
     */
    public function getSourceAttribute(): AttributeField;

    /**
     * Model where the relation is set to.
     *
     * @return string
     */
    public function getTargetModel(): string;

    /**
     * Return the main attribute where to start the relation to.
     *
     * @return AttributeField
     */
    public function getTargetAttribute(): AttributeField;

    /**
     * Return the source of the relation.
     *
     * @return Constraint
     */
    public function getSource(): Constraint;

    /**
     * Return the target of the relation.
     *
     * @return Constraint
     */
    public function getTarget(): Constraint;

    /**
     * Update a relation.
     *
     * @param LaramoreModel $model
     * @param array         $value
     * @return boolean
     */
    public function update(LaramoreModel $model, array $value): bool;

    /**
     * Delete relation.
     *
     * @param LaramoreModel $model
     * @return integer
     */
    public function delete(LaramoreModel $model): int;
}
