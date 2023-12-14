<?php

namespace Seatplus\Discord\Services\Roles;

use Illuminate\Support\Collection;
use Seatplus\Discord\Client\Guild;

class GetDiscordRoles
{
    private Guild $client;

    public function __construct()
    {
        $this->client = app(Guild::class);
    }

    public function execute(): Collection
    {

        // get all roles from guild
        return $this->client->getGuildRoles();
    }
}
