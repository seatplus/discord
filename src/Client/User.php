<?php

namespace Seatplus\Discord\Client;

class User
{
    private DiscordClient $client;

    public function __construct()
    {
        $this->client = app(DiscordClient::class);
    }

    public function getPrivateChannel(int $recipient_id): string
    {
        $response = $this->client->invoke('POST', 'users/@me/channels', [], [
            'recipient_id' => $recipient_id,
        ]);

        return $response->json('id');
    }
}
