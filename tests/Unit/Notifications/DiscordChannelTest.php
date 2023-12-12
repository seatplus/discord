<?php

it('sendsnotification', function () {

   // mock channel
    $channel_mock = $this->mock(\Seatplus\Discord\Client\Channel::class, function ($mock) {
        $mock->shouldReceive('send')
            ->once();
    });

    // mock the recipient
    $recipient = $this->mock(\Seatplus\BroadcastHub\Recipient::class);

    // mock the discord facade
    $discord_mock = $this->mock(\Seatplus\Discord\Discord::class, function ($mock) use ($recipient) {
        $mock->shouldReceive('getNotifiableId')
            ->once()
            ->with($recipient)
            ->andReturn('123456789');
    });

    // mock the notification
    $notification_mock = $this->mock(\Seatplus\Discord\Notifications\ErrorNotification::class, function ($mock) {
        $mock->shouldReceive('toBroadcaster')
            ->once()
            ->andReturn(
                new \Seatplus\Discord\Notifications\DiscordMessage('test')
            );
    });

    // create the channel
    $channel = new \Seatplus\Discord\Notifications\DiscordChannel($channel_mock, $discord_mock);

    // send the notification
    $channel->send($recipient, $notification_mock);

});

it('does not send if channel is not found', function () {
    // mock channel
    $channel_mock = $this->mock(\Seatplus\Discord\Client\Channel::class, function ($mock) {
        $mock->shouldReceive('send')
            ->never();
    });

    // mock the recipient
    $recipient = $this->mock(\Seatplus\BroadcastHub\Recipient::class);

    // mock the discord facade
    $discord_mock = $this->mock(\Seatplus\Discord\Discord::class, function ($mock) use ($recipient) {
        $mock->shouldReceive('getNotifiableId')
            ->once()
            ->with($recipient)
            ->andReturnNull();
    });

    // mock the notification
    $notification_mock = $this->mock(\Seatplus\Discord\Notifications\ErrorNotification::class, function ($mock) {
        $mock->shouldReceive('toBroadcaster')
            ->never();
    });

    // create the channel
    $channel = new \Seatplus\Discord\Notifications\DiscordChannel($channel_mock, $discord_mock);

    // send the notification
    $channel->send($recipient, $notification_mock);
});
