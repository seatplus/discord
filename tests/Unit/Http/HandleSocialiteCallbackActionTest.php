<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Seatplus\Discord\Discord;

it('creates user', function () {
    $socialite_user = \Mockery::mock(\SocialiteProviders\Manager\OAuth2\User::class);

    $socialite_user->shouldReceive('getId')
        ->andReturn('123456789');

    $socialite_user->shouldReceive('accessTokenResponseBody')
        ->andReturn([
            'guild' => [
                'id' => '123456789',
                'owner_id' => '987654321'
            ]
        ]);

    $provider = \Mockery::mock(\SocialiteProviders\Discord\Provider::class);

    $provider->shouldReceive('user')
        ->andReturn($socialite_user);

    \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')
        ->with('discord')
        ->andReturn($provider);

    Discord::getSettings()->getValue('guild_id');

    expect(Discord::getGuildId())->toBeNull();

    $action = new \Seatplus\Discord\Http\Actions\HandleSocialiteCallbackAction();

    $user = Event::fakeFor(fn() => \Seatplus\Auth\Models\User::factory()->create());

    \Illuminate\Support\Facades\Auth::login($user);

    $action->execute();

    expect(Discord::users()->count())->toBe(1);


});

it('kicks old user', function () {

    Http::fake();

    Http::assertNothingSent();

    $socialite_user = \Mockery::mock(\SocialiteProviders\Manager\OAuth2\User::class);

    $socialite_user->shouldReceive('getId')
        ->andReturn('123456789');

    // set guild_id to 123456789
    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', '123456789');

    // create user and act as that user
    $user = Event::fakeFor(fn() => \Seatplus\Auth\Models\User::factory()->create());

    \Illuminate\Support\Facades\Auth::login($user);

    // create a user with discord_id 123456789
    \Seatplus\Connector\Models\User::create([
        'user_id' => $user->id,
        'connector_id' => '999999999',
        'connector_type' => Discord::class
    ]);

    $provider = \Mockery::mock(\SocialiteProviders\Discord\Provider::class);

    $provider->shouldReceive('user')
        ->andReturn($socialite_user);

    \Laravel\Socialite\Facades\Socialite::shouldReceive('driver')
        ->with('discord')
        ->andReturn($provider);

    $action = new \Seatplus\Discord\Http\Actions\HandleSocialiteCallbackAction();

    $action->execute();

    \Illuminate\Support\Facades\Http::assertSentCount(1);
    \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        return $request->url() === 'https://discord.com/api/guilds/123456789/members/999999999' &&
            $request->method() === 'DELETE';
    });

});
