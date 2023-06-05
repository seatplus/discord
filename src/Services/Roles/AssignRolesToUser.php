<?php

namespace Seatplus\Discord\Services\Roles;

use Seatplus\Connector\Models\User;
use Seatplus\Discord\Discord;
use Seatplus\Discord\Services\Members\GetMemberAttribute;
use Seatplus\Discord\Services\Members\UpdateMemberAttribute;
use function PHPUnit\Framework\isNull;

class AssignRolesToUser
{
    public array $role_mappings;


    public function __construct(
        private string $guild_id = '',
        private ?BuildRoleControlGroupMap $buildRoleControlGroupMap = null
    )
    {
        $this->guild_id = Discord::getGuildId();

        if(is_null($this->buildRoleControlGroupMap)) {
            $this->buildRoleControlGroupMap = new BuildRoleControlGroupMap;
        }
    }

    /**
     * @throws \Exception
     */
    public function execute(User $user): void
    {

        $update_member_roles = new UpdateMemberAttribute($this->guild_id, $user->connector_id);
        $getMemberAttribute = new GetMemberAttribute($this->guild_id, $user->connector_id);

        // get current roles from user
        $roles = $user->roles()->pluck('name');

        // get all snowflake ids from discord meber
        $users_discord_roles = $getMemberAttribute->roles();

        // get all role_ids from role_mappings
        $all_role_ids = collect($this->getRoleMappings())->values();

        // get role_ids to keep
        $roles_to_keep = collect($users_discord_roles)->diff($all_role_ids);

        // get current role_ids from user that he should have
        $current_role_ids = $roles->map(fn ($role) => $this->getRoleMappings()[$role]);

        // update discord member to have the combination of roles_to_keep and current_role_ids
        $update_member_roles->roles($roles_to_keep->merge($current_role_ids)->toArray());

    }

    /**
     * @param array $role_mappings
     * @return AssignRolesToUser
     */
    public function setRoleMappings(array $role_mappings): AssignRolesToUser
    {
        $this->role_mappings = $role_mappings;
        return $this;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getRoleMappings(): array
    {
        if(!isset($this->role_mappings)) {

            $mapping = $this->buildRoleControlGroupMap->execute();

            $this->setRoleMappings($mapping);
        }

        return $this->role_mappings;
    }

}
