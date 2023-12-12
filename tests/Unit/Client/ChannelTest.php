<?php

use Illuminate\Support\Facades\Http;

it('can send a message', function () {

    Http::fake(fn() => Http::response(json_encode(['id' => 'bar'])));

    $channel = new \Seatplus\Discord\Client\Channel();

    $channel->send('123', [
        'content' => 'test',
    ]);

    \Illuminate\Support\Facades\Http::assertSent(function (\Illuminate\Http\Client\Request $request) {
        return $request->url() === 'https://discord.com/api/channels/123/messages';
    });

});
