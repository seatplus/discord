<?php

it('runs the command without error', function () {

    $this->mock(\Seatplus\Discord\Services\Members\UpdateUsersNick::class, function (Mockery\MockInterface $mock) {

        $mock->shouldReceive('execute')
            ->once();

        $mock->shouldReceive('hasError')
            ->once()
            ->andReturnFalse();

    });

    $this->artisan(\Seatplus\Discord\Commands\NicknameCommand::class)
        ->expectsOutput('Nicknames updated')
        ->assertSuccessful();

});

it('runs the command with error', function () {

    $this->mock(\Seatplus\Discord\Services\Members\UpdateUsersNick::class, function (Mockery\MockInterface $mock) {

        $mock->shouldReceive('execute')
            ->once();

        $mock->shouldReceive('hasError')
            ->once()
            ->andReturnTrue();

    });

    $this->artisan(\Seatplus\Discord\Commands\NicknameCommand::class)
        ->expectsOutput('There was an error updating the nicknames, check the logs!')
        ->assertFailed();

});
