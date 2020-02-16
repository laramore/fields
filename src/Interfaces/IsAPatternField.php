<?php
/**
 * Define a pattern field interface.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Interfaces;

interface IsAPatternField
{
    /**
     * Return the pattern to match.
     *
     * @return string
     */
    public function getPattern(): string;

    /**
     * Return all pattern flags
     *
     * @return mixed
     */
    public function getPatternFlags();
}
