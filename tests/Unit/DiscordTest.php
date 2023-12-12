<?php

use Seatplus\Discord\Discord;
use Seatplus\Discord\Notifications\ErrorNotification;
use Seatplus\Discord\Notifications\NewCorporationMember;

it('finds user', function () {

    $user = \Seatplus\Discord\Discord::findUser(1);

    expect($user)->toBeNull();
});

it('returns is tribe setup', function () {

    expect(\Seatplus\Discord\Discord::isTribeSetup())->toBeFalse();
});

it('returns registration url', function () {

    expect(\Seatplus\Discord\Discord::getRegistrationUrl())->toEqual(route('discord.register'));
});

it('returns connector route', function () {

    expect(\Seatplus\Discord\Discord::getConnectorConfigUrl())->toEqual("https://github.com/seatplus/discord#package_description");
});

it('returns is connector configured', function () {

    expect(\Seatplus\Discord\Discord::isConnectorConfigured())->toBeFalse();
});

it('returns is tribe enabled', function () {

    expect(\Seatplus\Discord\Discord::isTribeEnabled())->toBeFalse();
});

describe('Tribe Settings', function () {

    beforeEach(function () {
        Discord::getSettings()->getValue('guild_id');
    });

    it('enables tribe', function () {

        Discord::enableTribe();

        expect(Discord::isTribeEnabled())->toBeTrue();
    });

    it('disables tribe', function () {

        Discord::disableTribe();

        expect(Discord::isTribeEnabled())->toBeFalse();
    });

    it('sets tribe settings', function () {

        Discord::setTribeSettings(['test' => 'test']);

        expect(Discord::getTribeSettings())->toEqual(['test' => 'test']);
    });
});

it('gets NicknamePolicyCommandImplementation', function () {

    expect(Discord::getNicknamePolicyCommandImplementation())->toEqual(\Seatplus\Discord\Commands\NicknameCommand::class);
});

it('gets getSquadSyncCommandImplementation', function () {

    expect(Discord::getSquadSyncCommandImplementation())->toEqual(\Seatplus\Discord\Commands\SquadSyncCommand::class);
});

it('gets NotifiableId from channel', function () {

    // Mock Recipient
    $recipient = $this->mock(\Seatplus\BroadcastHub\Recipient::class, function (\Mockery\MockInterface $mock) {

        $mock->shouldReceive('getAttribute')
            ->with('name')
            ->once()
            ->andReturn('name_response');

        $mock->shouldReceive('getAttribute')
            ->with('connector_id')
            ->once()
            ->andReturn('connector_id_response');
    });

    expect(Discord::getNotifiableId($recipient))->toEqual('connector_id_response');
});

it('gets NotifiableID from user', function () {

        // Mock DiscordUser
        $this->mock(\Seatplus\Discord\Client\User::class, function (\Mockery\MockInterface $mock) {

            $mock->shouldReceive('getPrivateChannel')
                ->andReturn('discord_id');
        });

        // Mock Recipient
        $recipient = $this->mock(\Seatplus\BroadcastHub\Recipient::class, function (\Mockery\MockInterface $mock) {

            $mock->shouldReceive('getAttribute')
                ->with('name')
                ->once()
                ->andReturnNull();

            $mock->shouldReceive('getAttribute')
                ->with('connector_id')
                ->once()
                ->andReturn(42);
        });

        expect(Discord::getNotifiableId($recipient))->toEqual('discord_id');
});

it('gets getImplementedNotificationClasses', function () {

    expect(Discord::getImplementedNotificationClasses())->toEqual([
        NewCorporationMember::class,
        ErrorNotification::class,
    ]);
});

it('gets isBroadcasterEnabled', function () {

    expect(Discord::isBroadcasterEnabled())->toBeFalse();
});

it('gets getEnablePermission', function () {

    expect(Discord::getEnablePermission())->toEqual('superuser');
});

it('gets userCanEnableBroadcaster', function () {

    \Illuminate\Support\Facades\Event::fake();

    // create user
    $user = \Seatplus\Auth\Models\User::factory()->create();

    \Pest\Laravel\actingAs($user);

    expect(Discord::userCanEnableBroadcaster())->toBeFalse();
});

it('gets isConnectorSetup', function () {

    expect(Discord::isConnectorSetup())->toBeFalse();
});

it('stores BroadcasterSettings', function () {

    Discord::getSettings()->getValue('guild_id');
    Discord::storeBroadcasterSettings(['test' => 'test']);

    expect(Discord::getSettings()->getValue('broadcaster'))->toEqual(['test' => 'test']);
});

it('finds implemented notification ', function () {

        expect(Discord::findImplementedNotificationClass(\Seatplus\BroadcastHub\Notifications\NewCorporationMember::class))->toEqual(NewCorporationMember::class);

});

it('throws exception when implemented notification class not found', function () {

    expect(fn() => Discord::findImplementedNotificationClass('test'))->toThrow(\Exception::class);
});

it('get Channels', function () {

    // set guild id
    $guild_id = '123456789';
    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', $guild_id);

    // Mock Guild
    $this->mock(\Seatplus\Discord\Client\Guild::class, function (\Mockery\MockInterface $mock) {

        $mock->shouldReceive('getGuildChannels')
            ->andReturn([
                [
                    'id' => 'discord_id',
                    'name' => 'name',
                    'type' => 1,
                ],
                [
                    'id' => 'discord_id_2',
                    'name' => 'name_2',
                    'type' => 0,
                ]
            ]);
    });

    expect(Discord::getChannels())->toBeArray()->toHaveCount(1);
});
