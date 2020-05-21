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

use Laramore\Contracts\{
    Field\ManyRelationField, Eloquent\LaramoreMeta
};
use Laramore\Traits\Field\{
    ReversedRelation, ManyToManyRelation
};
use Laramore\Fields\BaseField;

class BelongsToMany extends BaseField implements ManyRelationField
{
    use ReversedRelation, ManyToManyRelation;

    /**
     * Return the pivot meta.
     *
     * @return LaramoreMeta
     */
    public function getPivotMeta(): LaramoreMeta
    {
        return $this->getReversed()->getPivotMeta();
    }

    /**
     * Return the pivot source.
     *
     * @return RelationField
     */
    public function getPivotSource(): RelationField
    {
        return $this->getReversed()->getPivotSource();
    }

    /**
     * Return the pivot target.
     *
     * @return RelationField
     */
    public function getPivotTarget(): RelationField
    {
        return $this->getReversed()->getPivotTarget();
    }

    /**
     * Return the pivot name.
     *
     * @return string
     */
    public function getPivotName(): string
    {
        return $this->getReversed()->getReversedPivotName();
    }
}
