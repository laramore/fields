<?php
/**
 * Define an indexable constraint contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field\Constraint;

interface IndexableConstraint extends Constraint
{
    /**
     * Find a model by its primary key.
     *
     * @param  mixed $id
     * @param  mixed $columns
     * @return \Laramore\Eloquent\LaramoreModel|null
     */
    public function find($id, $columns=['*']);

    /**
     * Return the model class used for this constraint.
     *
     * @return string
     */
    public function getModelClass(): string;
}
