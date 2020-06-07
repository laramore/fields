<?php
/**
 * Multiple source field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Field\Constraint\Constraint;

interface MultipleSourceField extends RelationField
{
    /**
     * Return the sources of the relation.
     *
     * @return array<Constraint>
     */
    public function getSources(): array;

    /**
     * Models where the relation is set from.
     *
     * @return array<string>
     */
    public function getSourceModels(): array;
}
