<?php
/**
 * Define a OneToMany field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Traits\Field\OneToRelation;
use Laramore\Contracts\Field\RelationField;

class OneToMany extends BaseComposed implements RelationField
{
    use OneToRelation;
}
