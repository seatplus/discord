<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('gets guild channels', function () {

    // fake http response
    Http::fake(fn () => Http::response(json_encode([
        [
            'id' => '123456789',
            'name' => 'general',
            'type' => 0,
        ],
        [
            'id' => '987654321',
            'name' => 'general',
            'type' => 0,
        ],
    ])));

    $guild = new \Seatplus\Discord\Client\Guild;
    $guild->setGuildId('123456789');

    expect($guild->getGuildChannels())->toBeArray();

    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://discord.com/api/guilds/123456789/channels';
    });

});

it('gets specific role by id', function () {

    // fake http response
    Http::fake(fn () => Http::response(json_encode([
        [
            'id' => '123456789',
            'name' => 'general',
            'type' => 0,
        ],
        [
            'id' => '987654321',
            'name' => 'general',
            'type' => 0,
        ],
    ])));

    $guild = new \Seatplus\Discord\Client\Guild(123456789);

    expect($guild->getGuildRoles('123456789'))->toBeArray()->toHaveCount(3);

    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://discord.com/api/guilds/123456789/roles';
    });

});

it('creates a role with specified position', function () {

    // fake http response
    Http::fake(fn () => Http::response(json_encode(['id' => 'bar'])));

    $guild = new \Seatplus\Discord\Client\Guild(123456789);

    expect($guild->createGuildRole('test', 1))->toBeArray()->toHaveCount(1);

    Http::assertSentCount(2);

    // assert first request
    Http::assertSent(fn (Request $request) => $request->url() === 'https://discord.com/api/guilds/123456789/roles' &&
               $request->method() === 'POST'
    );

    // assert second request
    Http::assertSent(fn (Request $request) => $request->url() === 'https://discord.com/api/guilds/123456789/roles' &&
               $request->method() === 'PATCH'
    );

});
