<?php

declare(strict_types=1);

namespace Rinvex\Addresses\Providers;

use Rinvex\Addresses\Models\Address;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Rinvex\Addresses\Console\Commands\MigrateCommand;
use Rinvex\Addresses\Console\Commands\PublishCommand;
use Rinvex\Addresses\Console\Commands\RollbackCommand;

class AddressesServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        \Rinvex\Addresses\Console\Commands\MigrateCommand::class,
        \Rinvex\Addresses\Console\Commands\PublishCommand::class,
        \Rinvex\Addresses\Console\Commands\RollbackCommand::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(realpath(__DIR__.'/../../config/config.php'), 'rinvex.addresses');

        // Bind eloquent models to IoC container
        $this->registerModels([
            'rinvex.addresses.address' => Address::class,
        ]);

        // Register console commands
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        // Konfigurationsdateien veröffentlichen
        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('rinvex.addresses.php'),
        ], 'config');

        // Migrations veröffentlichen
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
        ], 'migrations');

        // Migrations automatisch laden
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
