<?php

use Seatplus\Discord\Client\Guild;

it('throws exception if bot has not enough permissions', function () {

    $discord_roles = collect([
        [
            'id' => 123456789,
            'name' => 'testRole',
            'position' => 1,
        ],
    ]);

    $control_group_names = collect([
        'testRole',
    ]);

    $this->mock(Guild::class, function ($mock) {
        $mock->shouldReceive('modifyGuildRolePositions')
            ->andThrow(new \Exception('Something went wrong'));
    });

    // check if exception is thrown
    (new \Seatplus\Discord\Services\Roles\CheckBotPermissions)->check($discord_roles, $control_group_names);

})->throws(\Exception::class, 'Bot has not enough permissions to update roles');
