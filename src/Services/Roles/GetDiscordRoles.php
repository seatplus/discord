<?php

namespace Seatplus\Discord\Services\Roles;

use Illuminate\Support\Collection;
use Seatplus\Discord\Discord;


class GetDiscordRoles
{
    private string $guild_id;

    public function __construct()
    {
        $this->guild_id = Discord::getGuildId();
    }

    public function execute(): Collection
    {

        // get all roles from guild
        return (new \Seatplus\Discord\Client\Roles($this->guild_id))->get();
    }

}
