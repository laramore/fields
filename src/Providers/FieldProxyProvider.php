<?php
/**
 * Load and prepare proxies.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Container\Container;
use Laramore\Interfaces\{
	IsALaramoreManager, IsALaramoreProvider
};
use Laramore\Facades\FieldProxy;

class FieldProxyProvider extends ServiceProvider implements IsALaramoreProvider
{
    /**
     * Field manager.
     *
     * @var array
     */
    protected static $managers;

    /**
     * Before booting, create our definition for migrations.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/field/proxies.php', 'field.proxies',
        );

        $this->app->singleton('field_proxy', function() {
            return static::generateManager();
        });

        $this->app->booted([$this, 'bootedCallback']);
    }

    /**
     * Publish the config linked to fields.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/field/proxies.php' => $this->app->make('path.config').DIRECTORY_SEPARATOR.'field/proxies.php',
        ]);
    }

    /**
     * Return the default values for the manager of this provider.
     *
     * @return array
     */
    public static function getDefaults(): array
    {
        return [];
    }

    /**
     * Generate the corresponded manager.
     *
     * @return IsALaramoreManager
     */
    public static function generateManager(): IsALaramoreManager
    {
        $class = Container::getInstance()->config->get('field.proxies.manager');

        return new $class(static::getDefaults());
    }

    /**
     * Lock all managers after booting.
     *
     * @return void
     */
    public function bootedCallback()
    {
        FieldProxy::lock();
    }
}
