<?php
/**
 * Define a reverse manytomany field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Reversed;

use Laramore\Traits\Field\ManyToManyRelation;
use Laramore\Fields\BaseField;
use Laramore\Contracts\Field\{
    RelationField, Constraint\SourceConstraint, Constraint\TargetConstraint
};

class BelongsToMany extends BaseField
{
    use ManyToManyRelation;

    /**
     * Return the reversed field.
     *
     * @return RelationField
     */
    public function getReversed(): RelationField
    {
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
        $this->needsToBeOwned();

        return $this->getReversed()->getSourceModel();
    }

    /**
     * Return all attributes where to start the relation from.
     *
     * @return array<AttributeField>
     */
    public function getSourceAttributes(): array
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getSourceAttributes();
    }

    /**
     * Model where the relation is set to.
     *
     * @return string
     */
    public function getTargetModel(): string
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getTargetModel();
    }

    /**
     * Return all attributes where to start the relation to.
     *
     * @return array<AttributeField>
     */
    public function getTargetAttributes(): array
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getTargetAttributes();
    }

    /**
     * Return the source of the relation.
     *
     * @return SourceConstraint
     */
    public function getSource(): SourceConstraint
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getSource();
    }

    /**
     * Return the target of the relation.
     *
     * @return TargetConstraint
     */
    public function getTarget(): TargetConstraint
    {
        $this->needsToBeOwned();

        return $this->getReversed()->getTarget();
    }
}
