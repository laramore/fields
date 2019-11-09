<?php
/**
 * Define a pattern field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Validations\Pattern as PatternValidation;

abstract class Pattern extends Char
{
    protected $pattern;

    protected function setValidations()
    {
        parent::setValidations();

        $this->setValidation(PatternValidation::class)->pattern($this->pattern);
    }
}
