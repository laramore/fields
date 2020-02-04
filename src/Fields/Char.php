<?php
/**
 * Define a char field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Facades\Schema;
use Laramore\Facades\Rule;

class Char extends Text
{
    protected $maxLength;

    protected function __construct(array $rules=null)
    {
        parent::__construct($rules);

        $this->maxLength = Schema::getFacadeRoot()::$defaultStringLength;
    }

    public function maxLength(int $maxLength)
    {
        $this->needsToBeUnlocked();

        if ($maxLength <= 0) {
            throw new \Exception('The max length must be a positive number');
        }

        $this->defineProperty('maxLength', $maxLength);

        return $this;
    }

    public function transform($value)
    {
        $value = parent::transform($value);

        if ($this->maxLength < strlen($value) && !is_null($value)) {
            $dots = $this->hasRule(Rule::dotsOnResize()) ? '...' : '';

            if ($this->hasRule(Rule::caracterResize())) {
                $value = $this->resize($model, $attValue, $value, null, '', $dots);
            } else if ($this->hasRule(Rule::wordResize())) {
                $value = $this->resize($model, $attValue, $value, null, ' ', $dots);
            } else if ($this->hasRule(Rule::sentenceResize())) {
                $value = $this->resize($model, $attValue, $value, null, '.', $dots);
            }
        }

        return $value;
    }

    public function resize(string $value, $length=null, $delimiter='', $toAdd='')
    {
        $parts = $delimiter === '' ? str_split($value) : explode($delimiter, $value);
        $valides = [];
        $length = (($length ?: $this->maxLength) - strlen($toAdd));

        foreach ($parts as $part) {
            if (strlen($part) <= $length) {
                $length -= strlen($part);
                $valides[] = $part;
            } else {
                break;
            }
        }

        return implode($delimiter, $valides).$toAdd;
    }

    public function serialize($value)
    {
        return $value;
    }
}
