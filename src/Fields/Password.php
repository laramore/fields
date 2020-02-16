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
use Laramore\Facades\Option;
use Laramore\Interfaces\IsAPatternField;

class Password extends Char implements IsAPatternField
{
    protected $minLength = 8;

    /**
     * Return the pattern to match.
     *
     * @return string
     */
    public function getPattern(): string
    {
        return '/^\S*'.implode('', $this->getRegexOptions()).'\S*$/';
    }

    /**
     * Return all pattern flags
     *
     * @return mixed
     */
    public function getPatternFlags()
    {
        return null;
    }

    /**
     * Generate the regex options.
     *
     * @return string
     */
    protected function getRegexOptions()
    {
        $options = [];
        $patterns = $this->getConfig('patterns');

        if (!\is_null($this->minLength) || !\is_null($this->maxLength)) {
            $lengths = [$this->minLength ?: '', $this->maxLength ?: ''];
            $options[] = str_replace(['$min', '$max'], $lengths, $patterns['min_max_part']);
        }

        if ($this->hasOption(Option::needLowercase())) {
            $options[] = $patterns['one_lowercase_part'];
        }

        if ($this->hasOption(Option::needUppercase())) {
            $options[] = $patterns['one_uppercase_part'];
        }

        if ($this->hasOption(Option::needNumber())) {
            $options[] = $patterns['one_number_part'];
        }

        if ($this->hasOption(Option::needSpecial())) {
            $options[] = $patterns['one_special_part'];
        }

        return $options;
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
