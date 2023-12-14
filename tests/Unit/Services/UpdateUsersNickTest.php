<?php

use Seatplus\Discord\Discord;

it('has error if handleUser throws exception', function () {

    // set guild id
    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', 123456789);

    // create new User
    $user = Event::fakeFor(fn () => \Seatplus\Auth\Models\User::factory()->create());

    // create new connector_user
    \Seatplus\Connector\Models\User::create([
        'user_id' => $user->id,
        'connector_id' => '1',
        'connector_type' => Discord::class,
    ]);

    $this->mock(\Seatplus\Discord\Services\Members\ApplyNickPreAndPostFixToMember::class, function ($mock) {
        $mock->shouldReceive('execute')
            ->andThrow(new \Exception('Something went wrong'));
    });

    // expect to have 1 DiscordUser
    expect(Discord::users())->toHaveCount(1);

    $action = new \Seatplus\Discord\Services\Members\UpdateUsersNick();

    $action->execute();

    expect($action->hasError())->toBeTrue();

});
