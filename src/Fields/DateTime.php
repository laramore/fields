<?php
/**
 * Define a datetime field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Fields;

use Carbon\Carbon;
use Laramore\Facades\{
    Option, Type
};

class DateTime extends BaseAttribute
{
    protected $format;

    /**
     * Return the format for serialization.
     *
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format ?: $this->getConfig('format');
    }

    /**
     * Indicate if this field is timestamped.
     *
     * @return boolean
     */
    public function isTimestamped(): bool
    {
        return $this->getType() === Type::timestamp();
    }

    /**
     * Check all properties and options before locking the field.
     *
     * @return void
     */
    protected function checkOptions()
    {
        parent::checkOptions();

        if ($this->hasOption(Option::nullable()) && $this->hasOption(Option::useCurrent())) {
            throw new \Exception("This field must be either nullable or set by default as the current value");
        }
    }

    /**
     * Dry the value in a simple format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function dry($value)
    {
        return \is_null($value) ? null : (string) $value;
    }

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        return $this->transform($value);
    }

    /**
     * Transform the value to be used as a correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        return \is_null($value) ? null : new Carbon($value);
    }

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        if (\is_null($value)) {
            return $value;
        }

        $format = $this->getFormat();

        if ($format === Type::timestamp()->native) {
            return $value->getTimestamp();
        }

        return $value->format($this->getFormat());
    }
}
