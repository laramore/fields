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
    AttributeField, Constraint\SourceConstraint, Constraint\TargetConstraint
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
     * Reverbate the relation into database.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return boolean
     */
    public function reverbate(LaramoreModel $model, $value): bool;

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
     * Return all attributes where to start the relation from.
     *
     * @return array<AttributeField>
     */
    public function getSourceAttributes(): array;

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
     * Return all attributes where to start the relation to.
     *
     * @return array<AttributeField>
     */
    public function getTargetAttributes(): array;

    /**
     * Return the main attribute where to start the relation to.
     *
     * @return AttributeField
     */
    public function getTargetAttribute(): AttributeField;

    /**
     * Return the source of the relation.
     *
     * @return SourceConstraint
     */
    public function getSource(): SourceConstraint;

    /**
     * Return the target of the relation.
     *
     * @return TargetConstraint
     */
    public function getTarget(): TargetConstraint;
}
