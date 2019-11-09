<?php
/**
 * Load and prepare fields.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Providers;

use Illuminate\Support\ServiceProvider;

class FieldsProvider extends ServiceProvider
{
    /**
     * Publish the config linked to fields.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/fields.php' => config_path('fields.php'),
        ]);
    }

    /**
     * Merge the config file for fields.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/fields.php', 'fields',
        );
    }
}
