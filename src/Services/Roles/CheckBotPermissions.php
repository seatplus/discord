<?php

namespace Seatplus\Discord\Services\Roles;

use Illuminate\Support\Collection;
use Seatplus\Discord\Client\Guild;

class CheckBotPermissions
{
    private Guild $guild_client;

    public function __construct()
    {
        $this->guild_client = app(Guild::class);
    }

    public function check(Collection $discord_roles, Collection $control_group_names)
    {

        // remove all roles which are not in control group
        $roles = $discord_roles->filter(fn ($role) => $control_group_names->contains($role['name']));

        if ($roles->isEmpty()) {
            return;
        }

        // get the role with the highest position
        $highest_role = $roles->sortByDesc('position')->first();

        // try to update the role with the highest position in order to check if the bot has the permission to do so
        try {
            $this->guild_client->modifyGuildRolePositions($highest_role['id'], $highest_role['position']);
        } catch (\Exception $e) {
            throw new \Exception('Bot has not enough permissions to update roles');
        }

    }
}
