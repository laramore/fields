<?php
/**
 * Define a timestamp field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Laramore\Facades\Rule;

class Timestamp extends AttributeField
{
    /**
     * Check all properties and rules before locking the field.
     *
     * @return void
     */
    protected function checkRules()
    {
        parent::checkRules();

        if ($this->hasRule(Rule::nullable()) && $this->hasRule(Rule::useCurrent())) {
            throw new \Exception("This field must be either nullable or set by default as the current date");
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
        return \is_null($value) ? null : new Carbon($value);
    }

    /**
     * Transform the value to be used as a correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $this->dry($value);
    }
}
