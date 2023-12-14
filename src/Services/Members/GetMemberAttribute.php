<?php

namespace Seatplus\Discord\Services\Members;

use Seatplus\Discord\Client\Guild;

class GetMemberAttribute
{
    private Guild $guild_client;

    public function __construct(
        private string $user_id
    ) {
        $this->guild_client = app(Guild::class);
    }

    public function roles(): array
    {
        return $this->get('roles') ?? [];
    }

    public function nick(): string
    {
        return $this->get('nick');
    }

    private function get(string $key): array|string|bool|int|null
    {
        return $this->guild_client->getGuildMember($this->user_id, $key);
    }
}
