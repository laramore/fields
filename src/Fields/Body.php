<?php
/**
 * Define a specific text field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Str;
use Laramore\Facades\Option;

class Body extends Char
{
    /**
     * Transform the value to correspond to the field desire.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        if (\is_null($value)) {
            return $value;
        }

        $value = parent::transform($value);

        if ($this->hasOption(Option::uppercase())) {
            $value = $this->uppercase($value);
        } else if ($this->hasOption(Option::lowercase())) {
            $value = $this->lowercase($value);
        } else if ($this->hasOption(Option::title())) {
            $value = $this->title($value);
        } else if ($this->hasOption(Option::slug())) {
            $value = $this->slug($value);
        }

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

    public function uppercase(string $value): string
    {
        return Str::upper($value);
    }

    public function lowercase(string $value): string
    {
        return Str::lower($value);
    }

    public function title(string $value): string
    {
        return Str::title($value);
    }

    public function slug(string $value): string
    {
        return Str::slug($value);
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
    public function resize(string $value, integer $length=null, string $delimiter='', string $toAdd=''): string
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
}
