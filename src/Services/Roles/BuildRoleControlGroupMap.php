<?php

namespace Seatplus\Discord\Services\Roles;

use Illuminate\Support\Collection;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Discord\Client\Guild;
use Seatplus\Discord\Discord;

class BuildRoleControlGroupMap
{
    private Collection $control_group_names;

    private Collection $array_map;

    private CheckBotPermissions $check_bot_permissions;

    private GetDiscordRoles $get_discord_roles;

    private Guild $guild_client;

    public function __construct()
    {
        $this->control_group_names = Role::pluck('name');

        $this->get_discord_roles = new GetDiscordRoles;
        $this->check_bot_permissions = new CheckBotPermissions;
    }

    /**
     * @throws \Exception
     */
    public function execute(): array
    {

        $discord_roles = $this->get_discord_roles->execute();

        $this->check_bot_permissions->check($discord_roles, $this->control_group_names);

        $this->array_map = $discord_roles
            ->filter(fn ($role) => $this->control_group_names->contains($role['name']))
            ->mapWithKeys(fn ($role) => [$role['name'] => $role['id']]);

        $this->control_group_names
            // filter out roles which are already in discord
            ->filter(fn ($role) => ! $discord_roles->contains('name', $role))
            // create the roles
            ->each(function (string $role) {
                $created_role = $this->getDiscordGuildClient()->createGuildRole($role);
                $this->array_map->put($role, $created_role['id']);
            });

        return $this->array_map->toArray();

    }

    private function getDiscordGuildClient(): Guild
    {
        if (! isset($this->guild_client)) {
            $this->guild_client = app(Guild::class);
        }

        return $this->guild_client;
    }
}
