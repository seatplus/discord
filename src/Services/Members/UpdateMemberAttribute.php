<?php

namespace Seatplus\Discord\Services\Members;

use Seatplus\Discord\Client\Guild;

class UpdateMemberAttribute
{
    private Guild $guild_client;

    public function __construct(
        private string $user_id
    ) {
        $this->guild_client = app(Guild::class);
    }

    private function execute(array $attributes)
    {
        $this->guild_client->modifyGuildMember($this->user_id, [
            ...$attributes,
        ]);
    }

    public function nick(string $nick)
    {
        $this->execute([
            'nick' => $nick,
        ]);
    }

    public function roles(array $roles)
    {
        $this->execute([
            'roles' => $roles,
        ]);
    }
}
