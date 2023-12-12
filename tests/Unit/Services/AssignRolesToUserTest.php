<?php

use Seatplus\Discord\Discord;
use Seatplus\Discord\Services\Roles\AssignRolesToUser;
use Seatplus\Discord\Services\Roles\BuildRoleControlGroupMap;

it('gets role mappings', function () {

    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', 123456789);

    // mock BuildRoleControlGroupMap
    $buildRoleControlGroupMap = $this->mock(BuildRoleControlGroupMap::class, function ($mock) {
        $mock->shouldReceive('execute')
            ->andReturn([
                'testRole' => '123456789',
            ]);
    });

    $role_mappings = (new AssignRolesToUser($buildRoleControlGroupMap))->getRoleMappings();

    expect($role_mappings)->toBeArray();
});
