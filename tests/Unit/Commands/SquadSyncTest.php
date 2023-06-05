<?php

use Seatplus\Discord\Commands\SquadSyncCommand;
use Seatplus\Discord\Discord;
use Seatplus\Discord\Services\Roles\BuildRoleControlGroupMap;

beforeEach(function () {

    // set guild_id and user_id

    $this->guild_id = '1';

    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', $this->guild_id);

    $this->user_id = '1';

});

it('runs the command without error', function () {

    $this->mock(BuildRoleControlGroupMap::class, function (\Mockery\MockInterface $mock) {

        $mock->shouldReceive('execute')
            ->once()
            ->andReturn([
                'test' => '123'
            ]);

    });

    // Create Discord User
    $user = \Illuminate\Support\Facades\Event::fakeFor(fn() => \Seatplus\Auth\Models\User::factory()->create());

    $discord_user = \Seatplus\Connector\Models\User::create([
        'user_id' => $user->id,
        'connector_type' => Discord::class,
        'connector_id' => '123',
    ]);

    $this->mock(\Seatplus\Discord\Services\Roles\AssignRolesToUser::class, function (\Mockery\MockInterface $mock) use ($discord_user) {

        $mock->shouldReceive('setRoleMappings')
            ->once()
            ->with([
                'test' => '123'
            ])
            ->andReturn($mock
                ->shouldReceive('execute')
                ->once()
                ->getMock()
            );

    });

    $this->artisan(SquadSyncCommand::class)
        ->expectsOutput('Squads updated')
        ->assertSuccessful();

});

it('fails if role mapping throws error', function () {

        $this->mock(BuildRoleControlGroupMap::class, function (\Mockery\MockInterface $mock) {

            $mock->shouldReceive('execute')
                ->once()
                ->andThrow(new \Exception('test'));

        });

        $this->artisan(SquadSyncCommand::class)
            ->expectsOutput('There was an error creating the role mappings, check the logs!')
            ->assertFailed();
});

it('has errors if assignRolesToUser throws error', function () {

    $this->mock(BuildRoleControlGroupMap::class, function (\Mockery\MockInterface $mock) {

        $mock->shouldReceive('execute')
            ->once()
            ->andReturn([
                'test' => '123'
            ]);

    });

    // Create Discord User
    $user = \Illuminate\Support\Facades\Event::fakeFor(fn() => \Seatplus\Auth\Models\User::factory()->create());

    $discord_user = \Seatplus\Connector\Models\User::create([
        'user_id' => $user->id,
        'connector_type' => Discord::class,
        'connector_id' => '123',
    ]);

    $this->mock(\Seatplus\Discord\Services\Roles\AssignRolesToUser::class, function (\Mockery\MockInterface $mock) use ($discord_user) {

        $mock->shouldReceive('setRoleMappings')
            ->once()
            ->with([
                'test' => '123'
            ])
            ->andReturn($mock
                ->shouldReceive('execute')
                ->andThrow(new \Exception('test'))
                ->getMock()
            );

    });

    $this->artisan(SquadSyncCommand::class)
        ->expectsOutput('There was an error updating the squads, check the logs!')
        ->assertFailed();
});


