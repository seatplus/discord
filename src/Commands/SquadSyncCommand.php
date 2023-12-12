<?php

namespace Seatplus\Discord\Commands;

use Illuminate\Console\Command;
use Seatplus\Discord\Discord;
use Seatplus\Discord\Services\Roles\AssignRolesToUser;
use Seatplus\Discord\Services\Roles\BuildRoleControlGroupMap;

class SquadSyncCommand extends Command
{
    public $signature = 'tribe:squads:discord';

    public $description = 'Syncs squads to discord';

    private array $role_mappings;

    private bool $has_error = false;

    public function handle(AssignRolesToUser $assignRolesToUser, BuildRoleControlGroupMap $buildRoleControlGroupMap): int
    {

        try {
            $this->createRoleMappings($buildRoleControlGroupMap);
        } catch (\Exception $e) {

            report($e);
            $this->error('There was an error creating the role mappings, check the logs!');
            return self::FAILURE;
        }

        $assignRolesToUser = $assignRolesToUser->setRoleMappings($this->role_mappings);


        Discord::users()->each(function ($user) use ($assignRolesToUser) {
            try {
                // assign roles to user
                $assignRolesToUser->execute($user);
            } catch (\Exception $e) {
                // log the exception
                report($e);

                $this->setHasError();

                // skip the user
                return;
            }
        });



        if($this->has_error) {
            $this->error('There was an error updating the squads, check the logs!');
            return self::FAILURE;
        } else {
            $this->info('Squads updated');
        }

        return self::SUCCESS;
    }

    /**
     * @throws \Exception
     */
    private function createRoleMappings(BuildRoleControlGroupMap $buildRoleControlGroupMap)
    {
        $this->role_mappings = $buildRoleControlGroupMap->execute();
    }

    private function setHasError()
    {
        $this->has_error = true;
    }

}
