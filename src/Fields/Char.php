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
use Laramore\Facades\Option;

class Char extends Text
{
    protected $maxLength;

    /**
     * Create a new field with basic options.
     * The constructor is protected so the field is created writing left to right.
     * ex: Text::field()->maxLength(255) insteadof (new Text)->maxLength(255).
     *
     * Max length is defined by the default value.
     *
     * @param array|null $options
     */
    protected function __construct(array $options=null)
    {
        parent::__construct($options);

        $this->maxLength = Schema::getFacadeRoot()::$defaultStringLength;
    }

    /**
     * Define the max length for this field.
     *
     * @param integer $maxLength
     *
     * @return self
     */
    public function maxLength(int $maxLength)
    {
        $this->needsToBeUnlocked();

        if ($maxLength <= 0) {
            throw new \Exception('The max length must be a positive number');
        }

        $this->defineProperty('maxLength', $maxLength);

        return $this;
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

        if ($this->maxLength < strlen($value) && !is_null($value)) {
            $dots = $this->hasOption(Option::dotsOnResize()) ? '...' : '';

            if ($this->hasOption(Option::caracterResize())) {
                $value = $this->resize($value, null, '', $dots);
            } else if ($this->hasOption(Option::wordResize())) {
                $value = $this->resize($value, null, ' ', $dots);
            } else if ($this->hasOption(Option::sentenceResize())) {
                $value = $this->resize($value, null, '.', $dots);
            }
        }

        return $value;
    }

    /**
     * Resize a text value.
     *
     * @param string       $value
     * @param integer|null $length
     * @param string       $delimiter
     * @param string       $toAdd     If the value is resized.
     * @return string
     */
    public function resize(string $value, ?integer $length=null, string $delimiter='', string $toAdd=''): string
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

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $value;
    }
}
