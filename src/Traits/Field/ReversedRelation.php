<?php
/**
 * Trait to add reversed relation methods.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Laramore\Contracts\Field\{
    AttributeField, RelationField, Constraint\Constraint
};

trait ReversedRelation
{
    /**
     * Return the reversed field.
     *
     * @return RelationField
     */
    public function getReversed(): RelationField
    {
        $this->needsToBeOwned();

        return $this->getOwner();
    }

    /**
     * Indicate if the relation is head on or not.
     * Usefull to know which to use between source and target.
     *
     * @return boolean
     */
    public function isRelationHeadOn(): bool
    {
        return false;
    }

    /**
     * Model where the relation is set from.
     *
     * @return string
     */
    public function getSourceModel(): string
    {
        return $this->getReversed()->getTargetModel();
    }

    /**
     * Return the main attribute where to start the relation from.
     *
     * @return AttributeField
     */
    public function getSourceAttribute(): AttributeField
    {
        return $this->getReversed()->getTargetAttribute();
    }

    /**
     * Model where the relation is set to.
     *
     * @return string
     */
    public function getTargetModel(): string
    {
        return $this->getReversed()->getSourceModel();
    }

    /**
     * Return the main attribute where to start the relation to.
     *
     * @return AttributeField
     */
    public function getTargetAttribute(): AttributeField
    {
        return $this->getReversed()->getSourceAttribute();
    }

    /**
     * Return the source of the relation.
     *
     * @return Constraint
     */
    public function getSource(): Constraint
    {
        return $this->getReversed()->getTarget();
    }

    /**
     * Return the target of the relation.
     *
     * @return Constraint
     */
    public function getTarget(): Constraint
    {
        return $this->getReversed()->getSource();
    }
}
