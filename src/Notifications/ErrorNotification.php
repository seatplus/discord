<?php

namespace Seatplus\Discord\Notifications;

class ErrorNotification extends \Seatplus\BroadcastHub\Notifications\ErrorNotification
{

    protected static ?string $title = 'Discord Error Notification';

    public function via(): array
    {
        return [DiscordChannel::class];
    }

    public function toBroadcaster(): object
    {
        $message = DiscordMessage::create("Error {$this->code} sending notification to '{$this->recipient_name}' failed. Please check the logs for more information. And inform your administrator.");

        return $message->addEmbed([
            'title' => $this->notification_class,
            'description' => $this->error_message,
            'color' => 0xff0000,
        ]);
    }
}
