<?php

namespace Seatplus\Discord\Tests;

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
                default => dd('no match for '.$modelName)
            }
        );

        // Do not use the queue
        Queue::fake();

    }

    protected function getPackageProviders($app)
    {
        return [
            DiscordServiceProvider::class,
            ConnectorServiceProvider::class,
            EveapiServiceProvider::class,
            AuthenticationServiceProvider::class,
            BroadcastHubServiceProvider::class,
            TribeServiceProvider::class,
            ConnectorServiceProvider::class
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config(['app.debug' => true]);

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'seatplus_tests---');

        $app['config']->set('services.discord.bot_token', 'token');

        //Setup Inertia for package development
        /*config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            [
                realpath(__DIR__ . '/../src/resources/js/Pages'),
                realpath(__DIR__ . '/../src/resources/js/Shared')
            ],
        ));*/
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
