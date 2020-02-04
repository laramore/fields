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
use Laramore\Facades\Rule;

class Password extends Pattern
{
    protected $minLength = 8;

    /**
     * Return the pattern to match.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return '/^\S*'.implode('', $this->getRegexRules()).'\S*$/';
    }

    /**
     * Generate the regex rules.
     *
     * @return string
     */
    protected function getRegexRules()
    {
        $rules = [];
        $patterns = $this->getConfig('patterns');

        if (!\is_null($this->minLength) || !\is_null($this->maxLength)) {
            $lengths = [$this->minLength ?: '', $this->maxLength ?: ''];
            $rules[] = str_replace(['$min', '$max'], $lengths, $patterns['min_max_part']);
        }

        if ($this->hasRule(Rule::needLowercase())) {
            $rules[] = $patterns['one_lowercase_part'];
        }

        if ($this->hasRule(Rule::needUppercase())) {
            $rules[] = $patterns['one_uppercase_part'];
        }

        if ($this->hasRule(Rule::needNumber())) {
            $rules[] = $patterns['one_number_part'];
        }

        if ($this->hasRule(Rule::needSpecial())) {
            $rules[] = $patterns['one_special_part'];
        }

        return $rules;
    }

    /**
     * Transform the value to be used as a correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        $value = parent::transform($value);

        if (\is_null($value)) {
            return $value;
        }

        return $this->hash($value);
    }

    /**
     * Hash the password so it is not retrievible.
     *
     * @param string $value
     * @return string
     */
    public function hash(string $value)
    {
        return Hash::make($value);
    }

    /**
     * Indicate if the password is the right one.
     *
     * @param string  $value
     * @param string  $password
     * @param boolean $expected
     * @return boolean
     */
    public function isCorrect(string $value, string $password=null, bool $expected=true)
    {
        return Hash::check($password, $value) === $expected;
    }
}
