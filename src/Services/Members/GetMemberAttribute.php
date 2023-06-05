<?php

namespace Seatplus\Discord\Services\Members;

class GetMemberAttribute
{
    private \Seatplus\Discord\Client\Member $client;

    public function __construct($guild_id, $user_id)
    {
        $this->client = new \Seatplus\Discord\Client\Member($guild_id, $user_id);
    }

    public function roles(): array
    {
        return $this->get('roles');
    }

    public function nick(): string
    {
        return $this->get('nick');
    }

    private function get(string $key): array|string|bool|int|null
    {
        return $this->client->get($key);
    }

}
