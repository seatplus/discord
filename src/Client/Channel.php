<?php

namespace Seatplus\Discord\Client;

class Channel
{

    private DiscordClient $client;

    public function __construct()
    {
        $this->client = app(DiscordClient::class);
    }

    public function send(string $channel_id, array $data): array
    {
        return $this->client->invoke('POST', 'channels/{channel.id}/messages', [
            'channel.id' => $channel_id,
        ], $data)->json();
    }

}
