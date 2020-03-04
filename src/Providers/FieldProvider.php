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
use Laramore\Traits\Provider\MergesConfig;

class FieldProvider extends ServiceProvider
{
    use MergesConfig;

    /**
     * Before booting, create our definition for migrations.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/field.php', 'field',
        );

        $this->mergeConfigFrom(
            __DIR__.'/../../config/field/proxies.php', 'field.proxies',
        );
    }

    /**
     * Publish the config linked to fields.
     *
     * @return void
     */
    public function boot()
    {
        $pathDir = $this->app->make('path.config');

        $this->publishes([
            __DIR__.'/../../config/field.php' => $pathDir.'/field.php',
            __DIR__.'/../../config/field/proxies.php' => $pathDir.'/field/proxies.php',
        ]);
    }
}
