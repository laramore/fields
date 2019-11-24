<?php
/**
 * Define a email field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laramore\Facades\Rules;

class Password extends Pattern
{
    protected $minLength = 8;

    public function getPattern(): string
    {
        return '/^\S*'.implode('', $this->getRegexRules()).'\S*$/';
    }

    protected function getRegexRules()
    {
        $rules = [];
        $patterns = $this->getConfig('patterns');

        if (!\is_null($this->minLength) || !\is_null($this->maxLength)) {
            $lengths = [$this->minLength ?: '', $this->maxLength ?: ''];
            $rules[] = str_replace(['$min', '$max'], $lengths, $patterns['min_max_part']);
        }

        if ($this->hasRule(Rules::needLowercase())) {
            $rules[] = $patterns['one_lowercase_part'];
        }

        if ($this->hasRule(Rules::needUppercase())) {
            $rules[] = $patterns['one_uppercase_part'];
        }

        if ($this->hasRule(Rules::needNumber())) {
            $rules[] = $patterns['one_number_part'];
        }

        if ($this->hasRule(Rules::needSpecial())) {
            $rules[] = $patterns['one_special_part'];
        }

        return $rules;
    }

    public function transform($value)
    {
        $value = parent::transform($value);

        if (\is_null($value)) {
            return $value;
        }

        return $this->hash($value);
    }

    public function hash(string $value)
    {
        return Hash::make($value);
    }

    public function isCorrect(string $value, string $password=null, bool $expected=true)
    {
        return Hash::check($password, $value) === $expected;
    }
}
