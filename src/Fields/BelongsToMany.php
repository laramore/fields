<?php
/**
 * Define a reverse manytomany field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Traits\Field\ManyToManyRelation;
use Laramore\Fields\BaseComposed;

class BelongsToMany extends BaseLink
{
    use ManyToManyRelation;

    /**
     * Return the reversed field.
     *
     * @return BaseComposed
     */
    public function getReversed(): BaseComposed
    {
        return $this->getOwner();
    }

    /**
     * Define values after the field is owned.
     *
     * @return void
     */
    protected function owned()
    {
        parent::owned();

        $this->defineProperty('pivotMeta', $this->getReversed()->pivotMeta);
        $this->defineProperty('pivotTo', $this->getReversed()->pivotFrom);
        $this->defineProperty('pivotFrom', $this->getReversed()->pivotTo);
        $this->defineProperty('pivotName', $this->getReversed()->reversedPivotName);
    }
}
