<?php

use Illuminate\Support\Facades\Notification as Notification;
use Seatplus\BroadcastHub\Recipient;

it('sends notification', function () {

    Notification::fake();

    $corporation = \Seatplus\Eveapi\Models\Corporation\CorporationInfo::factory()->create();

    $recipient = Recipient::factory()->create();

    $new_corporation_members = [
        [
            'start_date' => now()->toDateTimeString(),
            'character' => [
                'character_id' => 1,
                'name' => 'test',
            ],
        ],
        [
            'start_date' => now()->toDateTimeString(),
            'character' => [
                'character_id' => 2,
                'name' => 'test2',
            ],
        ]
    ];

    $notification = new \Seatplus\Discord\Notifications\NewCorporationMember($new_corporation_members, $corporation);

    Notification::send($recipient, $notification);

    Notification::assertSentTo($recipient, \Seatplus\Discord\Notifications\NewCorporationMember::class);

});

it('creates DiscordMessage', function () {

    $corporation = \Seatplus\Eveapi\Models\Corporation\CorporationInfo::factory()->create();

    $new_corporation_members = [
        [
            'start_date' => now()->toDateTimeString(),
            'character' => [
                'character_id' => 1,
                'name' => 'test',
            ],
        ],
        [
            'start_date' => now()->toDateTimeString(),
            'character' => [
                'character_id' => 2,
                'name' => 'test2',
            ],
        ]
    ];

    $notification = new \Seatplus\Discord\Notifications\NewCorporationMember($new_corporation_members, $corporation);

    $message = $notification->toBroadcaster();

    expect($message)->toBeInstanceOf(\Seatplus\Discord\Notifications\DiscordMessage::class);

    expect($message->toArray())->toHaveCount(2);

});
