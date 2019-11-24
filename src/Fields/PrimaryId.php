<?php
/**
 * Define a primary id field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Interfaces\IsAPrimaryField;

class PrimaryId extends Increment implements IsAPrimaryField
{
}
