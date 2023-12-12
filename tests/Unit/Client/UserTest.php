<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('gets Private Channel of recipient', function () {

    // fake http client
    Http::fake(fn () => Http::response(json_encode([
        'id' => '123456789',
        'type' => 1,
        'recipients' => [
            'id' => '987654321',
            'username' => 'test',
            'avatar' => 'test',
            'discriminator' => 'test',
            'public_flags' => 0,
        ],
        'last_message_id' => '123456789',
        'last_pin_timestamp' => '2021-08-01T00:00:00.000000+00:00',
    ])));

    $user = new \Seatplus\Discord\Client\User();

    $user->getPrivateChannel(987654321);

    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://discord.com/api/users/@me/channels' &&
            $request->method() === 'POST';
    });
});
