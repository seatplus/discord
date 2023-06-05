<?php

namespace Seatplus\Discord\Services\Members;

class UpdateMemberAttribute
{
    private \Seatplus\Discord\Client\Member $client;

    public function __construct($guild_id, $user_id)
    {
        $this->client = new \Seatplus\Discord\Client\Member($guild_id, $user_id);
    }

    private function execute(array $attributes)
    {
        $this->client->update([
            ...$attributes
        ]);
    }

    public function nick(string $nick)
    {
        $this->execute([
            'nick' => $nick
        ]);
    }

    public function roles(array $roles)
    {
        $this->execute([
            'roles' => $roles
        ]);
    }

}
