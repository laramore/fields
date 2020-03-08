<?php
/**
 * Load and prepare constraints.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Container\Container;
use Laramore\Contracts\{
	Manager\LaramoreManager, Provider\LaramoreProvider
};
use Laramore\Facades\FieldConstraint;

class FieldConstraintProvider extends ServiceProvider implements LaramoreProvider
{
    /**
     * Before booting, create our definition for migrations.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/field/constraints.php', 'field.constraints',
        );

        $this->app->singleton('field_constraint', function() {
            return static::generateManager();
        });

        $this->app->booted([$this, 'booted']);
    }

    /**
     * Publish the config linked to fields.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/field/constraints.php' => $this->app->make('path.config').'/field/constraints.php',
        ]);
    }

    /**
     * Return the default values for the manager of this provider.
     *
     * @return array
     */
    public static function getDefaults(): array
    {
        return Container::getInstance()->config->get('field.constraints.configurations');
    }

    /**
     * Generate the corresponded manager.
     *
     * @return LaramoreManager
     */
    public static function generateManager(): LaramoreManager
    {
        $class = Container::getInstance()->config->get('field.constraints.manager');

        return new $class(static::getDefaults());
    }

    /**
     * Lock all managers after booting.
     *
     * @return void
     */
    public function booted()
    {
        FieldConstraint::lock();
    }
}