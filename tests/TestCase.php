<?php

namespace Seatplus\Discord\Tests;

use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as Orchestra;
use Seatplus\Auth\AuthenticationServiceProvider;
use Seatplus\Auth\Models\User;
use Seatplus\BroadcastHub\BroadcastHubServiceProvider;
use Seatplus\Connector\ConnectorServiceProvider;
use Seatplus\Discord\DiscordServiceProvider;
use Seatplus\Eveapi\EveapiServiceProvider;
use Seatplus\Tribe\TribeServiceProvider;

class TestCase extends Orchestra
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => match (true) {
                Str::startsWith($modelName, 'Seatplus\Auth') => 'Seatplus\\Auth\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Eveapi') => 'Seatplus\\Eveapi\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Tribe') => 'Seatplus\\Tribe\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Discord') => 'Seatplus\\Discord\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\BroadcastHub') => 'Seatplus\\BroadcastHub\\Database\\Factories\\'.class_basename($modelName).'Factory',
                default => dd("no match for $modelName")
            }
        );

        // Do not use the queue
        Queue::fake();

    }

    protected function getPackageProviders($app)
    {
        return [
            ConnectorServiceProvider::class,
            EveapiServiceProvider::class,
            AuthenticationServiceProvider::class,
            BroadcastHubServiceProvider::class,
            TribeServiceProvider::class,
            DiscordServiceProvider::class,
        ];
    }

    public function defineEnvironment($app)
    {

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        config(['app.debug' => true]);

        tap($app->make('config'), function (Repository $config) {

            $config->set('app.debug', true);
            $config->set('app.env', 'testing');
            $config->set('cache.prefix', 'seatplus_tests---');

            // Use test User model for users provider
            $config->set('auth.providers.users.model', User::class);

            // set discord bot token
            $config->set('services.discord.bot_token', 'token');
        });

    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        //$app->singleton('Illuminate\Contracts\Http\Kernel', Kernel::class);
    }
}
