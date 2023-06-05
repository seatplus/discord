<?php

use Illuminate\Support\Facades\Http;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Discord\Discord;
use Illuminate\Http\Client\Request;
use Seatplus\Discord\Services\Roles\AssignRolesToUser;

beforeEach(function () {

    // set guild_id and user_id

    $this->guild_id = '1';

    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', $this->guild_id);

    $this->user_id = '1';

    $this->role = Role::create(['name' => 'testRole']);

});

it('get discord roles', function () {

    // fake http response
    Http::fake(fn() => Http::response(test()->getDiscordRolesMock()));

    $get_discord_roles = new \Seatplus\Discord\Services\Roles\GetDiscordRoles;

    expect($get_discord_roles->execute($this->guild_id))->toBeCollection();

    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://discord.com/api/guilds/1/roles';
    });

});

it('checks if bot has permissions', function () {

    $discord_roles = collect(test()->getDiscordRolesMock());

    // get the highest role
    $highest_role = $discord_roles->sortByDesc('position')->first();

    // create a role with the same name as the highest role
    Role::create(['name' => $highest_role['name']]);

    // get all role names
    $control_group_names = Role::pluck('name');

    // fake http response
    Http::fake();

    (new \Seatplus\Discord\Services\Roles\CheckBotPermissions)->check($discord_roles, $control_group_names);

    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://discord.com/api/guilds/1/roles';
    });

});

it('builds Role Control Group Map and creates discord role', function () {

    // Here is what we expect to happen:
    // 1. Get all discord roles
    // 2. No bot permissions are checked
    // 3. we create 1 discord role
    // finally we expect the role control group map to be an array with 1 entry

    // fake http response
    Http::fakeSequence()
        // 1. Get all discord roles
        ->push(test()->getDiscordRolesMock())
        // 2. No bot permissions are checked - no http call to be expected
        // 3. we create 1 discord role - and return it
        ->push(test()->getDiscordRolesMock()[0]);

    $role_control_group_map = new \Seatplus\Discord\Services\Roles\BuildRoleControlGroupMap();

    expect($role_control_group_map->execute())->toBeArray();

    Http::assertSentCount(2);

});

it('assigns roles to user if user has role', function (bool $hasRole) {

    // define role mapping
    $role_mapping = [
        'testRole' => '10',
    ];

    // create connector_user
    $user = \Illuminate\Support\Facades\Event::fakeFor(fn() => \Seatplus\Auth\Models\User::factory()->create());
    $connector_user = \Seatplus\Connector\Models\User::create([
            'user_id' => $user->id,
            'connector_id' => $this->user_id,
            'connector_type' => Discord::class,
        ]);

    // prepare the action
    $assign_roles_to_user = (new AssignRolesToUser)->setRoleMappings($role_mapping);

    if($hasRole) {
        // assign Role to user
        $user->assignRole($this->role);
    }


    // fake http response
    Http::fakeSequence()
        // 1. member roles are fetched via member object
        ->push(test()->getDiscordMember())
        // 2. member roles are updated, no response expected
        ->push(null);

    // execute the action
    $assign_roles_to_user->execute($connector_user, $this->guild_id);

    // assert the second request
    $recorded = Http::recorded();

    [$request, $response] = $recorded[1];

    expect($request['roles'])->toBeArray();

    if($hasRole) {
        expect($request['roles'])->toContain('10', '20', '30');
    } else {
        expect($request['roles'])
            ->toContain('20', '30')
            ->not->toContain('10');
    }

})->with([true, false]);

