<?php

it('can set body', function () {
    $message = \Seatplus\Discord\Notifications\DiscordMessage::create()
        ->setBody('test');

    expect($message->body)->toEqual('test');
});

it('can set embeds', function () {
    $message = \Seatplus\Discord\Notifications\DiscordMessage::create()
        ->setEmbeds([
            [
                'title' => 'test',
                'description' => 'test',
                'color' => 0xFF0000,
            ],
        ]);

    expect($message->embeds)->toHaveCount(1);
});
