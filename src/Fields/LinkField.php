<?php
/**
 * Define a link field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Interfaces\{
    IsAnOwner, IsARelationField
};
use Laramore\Fields\BaseField;

abstract class LinkField extends BaseField implements IsARelationField
{
    /**
     * Set the owner.
     *
     * @param IsAnOwner $owner
     * @return void
     */
    protected function setOwner(IsAnOwner $owner)
    {
        if (\is_null($this->off)) {
            throw new \Exception('You need to specify `off`');
        }

        $this->setMeta($this->off::getMeta());

        parent::setOwner($owner);
    }

    /**
     * Callaback when the instance is owned.
     *
     * @return void
     */
    protected function owned()
    {
        parent::owned();

        $this->getMeta()->setLink($this->name, $this);
    }
}
