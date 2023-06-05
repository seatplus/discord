<?php

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\Provider;
use Seatplus\Discord\Discord;

beforeEach(function () {


});

it('creates AbstractProvider with bot scopes', function () {

    $provider = \Mockery::mock(\SocialiteProviders\Discord\Provider::class);

    $provider->shouldReceive('setScopes')
        ->with(\Seatplus\Discord\Http\Actions\GetSocialiteProviderAction::BOT_SCOPES)
        ->andReturn($provider);

    $provider->shouldReceive('with')
        ->once()
        ->andReturn($provider);

    Socialite::shouldReceive('driver')
        ->with('discord')
        ->andReturn($provider);

    $action = new \Seatplus\Discord\Http\Actions\GetSocialiteProviderAction();

    // expect Discord::getGuildId() to be null
    expect(\Seatplus\Discord\Discord::getGuildId())->toBeNull();

    // execute the action

    $result = $action->execute();

    // assert that the result is an instance of AbstractProvider
    expect($result)->toBeInstanceOf(\SocialiteProviders\Manager\OAuth2\AbstractProvider::class);

});

it('creates AbstractProvider with guild scopes', function () {

    $provider = \Mockery::mock(\SocialiteProviders\Discord\Provider::class);

    $provider->shouldReceive('setScopes')
        ->with(\Seatplus\Discord\Http\Actions\GetSocialiteProviderAction::SCOPES)
        ->andReturn($provider);

    Socialite::shouldReceive('driver')
        ->with('discord')
        ->andReturn($provider);

    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', '123456789');

    $action = new \Seatplus\Discord\Http\Actions\GetSocialiteProviderAction();

    // expect Discord::getGuildId() not to be null
    expect(\Seatplus\Discord\Discord::getGuildId())->not->toBeNull();

    // execute the action

    $result = $action->execute();

    // assert that the result is an instance of AbstractProvider
    expect($result)->toBeInstanceOf(\SocialiteProviders\Manager\OAuth2\AbstractProvider::class);

});
