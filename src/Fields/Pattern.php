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

abstract class Pattern extends Char
{
    /**
     * Return the pattern to match.
     *
     * @return string
     */
    abstract public function getPattern(): string;

    /**
     * Return all pattern flags
     *
     * @return mixed
     */
    public function getPatternFlags()
    {
        return null;
    }
}
