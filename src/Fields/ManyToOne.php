<?php
/**
 * Define a foreign field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Traits\Field\ToOneRelation;
use Laramore\Contracts\Field\RelationField;

class ManyToOne extends BaseComposed implements RelationField
{
    use ToOneRelation;
}
