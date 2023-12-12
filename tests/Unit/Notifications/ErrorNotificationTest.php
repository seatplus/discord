<?php

use Seatplus\BroadcastHub\Notifications\ErrorNotification;

beforeEach(function () {
    $this->recipient = \Seatplus\BroadcastHub\Recipient::factory()->create();

    $this->notification = new \Seatplus\Discord\Notifications\ErrorNotification(
        recipient: $this->recipient,
        message: $this->mock(ErrorNotification::class),
        exception: new \Exception('test')
    );
});

it('sends global erro notification', function () {

    Notification::fake();

    Notification::send($this->recipient, $this->notification);

    Notification::assertSentTo($this->recipient, \Seatplus\Discord\Notifications\ErrorNotification::class);

});

it('creates DiscordMessage', function () {

    $message = $this->notification->toBroadcaster();

    expect($message)->toBeInstanceOf(\Seatplus\Discord\Notifications\DiscordMessage::class);
    expect($message->toArray())->toHaveCount(2);

});
