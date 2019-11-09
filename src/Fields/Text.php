<?php
/**
 * Define a text field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Facades\Schema;
use Laramore\Validations\NotBlank;
use Laramore\Elements\Type;
use Rules, Types;

class Text extends Field
{

    protected function setValidations()
    {
        parent::setValidations();

        if ($this->hasRule(Rules::notBlank())) {
            $this->setValidation(NotBlank::class);
        }
    }

    public function dry($value)
    {
        return $this->transform($value);
    }

    public function cast($value)
    {
        return $this->transform($value);
    }

    public function transform($value)
    {
        return is_null($value) ? $value : (string) $value;
    }

    public function serialize($value)
    {
        return $value;
    }
}
