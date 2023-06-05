<?php

namespace Seatplus\Discord;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\SocialiteManager;
use Seatplus\Discord\Commands\NicknameCommand;
use Seatplus\Discord\Commands\SquadSyncCommand;
use Seatplus\Tribe\TribeRepository;
use SocialiteProviders\Discord\DiscordExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Discord\Provider;

class DiscordServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the JS & CSS,
        $this->addPublications();

        // Add routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        //Add Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');

        // Add translations
        //$this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'web');

        // Add commands
        $this->addCommands();

        $tribe = app(TribeRepository::class);
        $tribe->add(new Discord());

        Event::listen(SocialiteWasCalled::class, DiscordExtendSocialite::class.'@handle');

    }

    public function register()
    {
        $this->mergeConfigurations();

        // Register the Socialite Factory.
        // From: Laravel\Socialite\SocialiteServiceProvider
        if (! $this->app->bound('Laravel\Socialite\Contracts\Factory')) {
            $this->app->singleton('Laravel\Socialite\Contracts\Factory', fn($app) => new SocialiteManager($app));
        }

        // Slap in the Telegram Socialite Provider
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');

        $socialite->extend(
            'discord',
            function ($app) use ($socialite) {
                $config = $app['config']['services.discord'];

                return $socialite->buildProvider(Provider::class, $config);
            }
        );


    }

    private function mergeConfigurations()
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/discord.services.php', 'services'
        );
    }

    private function addPublications()
    {
        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__ . '/../public/img' => public_path('img'),
        ], 'web');
    }

    private function addCommands()
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                NicknameCommand::class,
                SquadSyncCommand::class,
            ]);
        }
    }
}
