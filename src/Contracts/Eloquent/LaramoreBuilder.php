<?php
/**
 * Laramore builder.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Eloquent;

use Laramore\Contracts\Proxied;

interface LaramoreBuilder extends Proxied
{
    /**
     * Get the underlying query builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getQuery();
}
