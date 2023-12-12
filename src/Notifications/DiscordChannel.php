<?php

namespace Seatplus\Discord\Notifications;

use Seatplus\BroadcastHub\Contracts\Notification;
use Seatplus\BroadcastHub\Recipient;
use Seatplus\Discord\Client\Channel;
use Seatplus\Discord\Discord;

class DiscordChannel implements \Seatplus\BroadcastHub\Contracts\Channel
{
    public function __construct(
        private Channel $channel,
        private ?Discord $discord = null
    )
    {
        $this->discord ??= app(Discord::class);
    }

    public function send(Recipient $recipient, Notification $notification): void
    {

        $channel = $this->discord->getNotifiableId($recipient);

        if (is_null($channel)) {
            return;
        }

        $message = $notification->toBroadcaster();

        $this->channel->send($channel, $message->toArray());

    }

}
