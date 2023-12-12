<?php

use Composer\InstalledVersions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Seatplus\Discord\Discord;

it('builds client with dev version', function () {

    // Mock the InstalledVersions class
    $installedVersionsWrapperMock = $this->mock(\Seatplus\Discord\Client\InstalledVersionsWrapper::class, function ($mock) {
        // Make the getPrettyVersion method throw an OutOfBoundsException
        $mock->shouldReceive('getPrettyVersion')
            ->with('seatplus/discord')
            ->andThrow(new OutOfBoundsException('Package seatplus/discord is not installed'));
    });

    // Create a new DiscordClient
    $client = new \Seatplus\Discord\Client\DiscordClient('token', $installedVersionsWrapperMock);

    expect($client->client->getOptions()['headers'])->toHaveKey('User-Agent', 'Seatplus Discord Connector v.dev');
});

describe('Upon invoking the client, handle client errors', function () {

    it('deletes user', function () {

        // fake http response
        Http::fake(fn() => Http::response(json_encode([
            'code' => 10007,
            'message' => 'Unknown User',
        ]), 400));

        $client = new \Seatplus\Discord\Client\DiscordClient('token');

        $client->invoke('get', 'users/{user.id}', ['user.id' => '123456789']);

        Http::assertSentCount(1);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://discord.com/api/users/123456789' &&
                $request->method() === 'GET';
        });
    });

    it('deletes guild', function () {

        // set guild id
        $guild_id = '123456789';
        Discord::getSettings()->getValue('guild_id');
        Discord::getSettings()->setValue('guild_id', $guild_id);

        expect(Discord::getSettings()->getValue('guild_id'))->toEqual($guild_id);

        // fake http response
        Http::fake(fn() => Http::response(json_encode([
            'code' => 10004,
            'message' => 'Unknown Guild',
        ]), 400));

        $client = new \Seatplus\Discord\Client\DiscordClient('token');

        $client->invoke('get', 'guilds/{guild.id}', ['guild.id' => $guild_id]);

        Http::assertSentCount(1);

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://discord.com/api/guilds/123456789' &&
                $request->method() === 'GET';
        });
    });

    it('throws exception', function () {

        // fake http response
        Http::fake(fn() => Http::response(json_encode([
            'code' => 10000,
            'message' => 'Unknown Error',
        ]), 400));

        $client = new \Seatplus\Discord\Client\DiscordClient('token');

        $client->invoke('get', 'guilds/{guild.id}', ['guild.id' => '123456789']);

        Http::assertSentCount(1);

    })->throws(\Illuminate\Http\Client\RequestException::class);
});
