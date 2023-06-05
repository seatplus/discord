<?php

namespace Seatplus\Discord;

use Seatplus\Connector\Contracts\Connector;
use Seatplus\Connector\Models\Settings;
use Seatplus\Connector\Models\User;
use Seatplus\Discord\Commands\NicknameCommand;
use Seatplus\Discord\Commands\SquadSyncCommand;
use Seatplus\Tribe\Contracts\Tribe;

class Discord implements Tribe, Connector
{

    public static function getName(): string
    {
        return 'discord';
    }

    public static function getImg(): string
    {
        return asset('img/discord-gray-900.svg');
    }

    public static function users(): \Illuminate\Support\Collection
    {
        return User::query()
            ->where('connector_type', self::class)
            ->get();
    }

    public static function findUser(int $user_id): ?User
    {
        return User::query()
            ->where('connector_type', self::class)
            ->firstWhere('user_id', $user_id);
    }

    public static function getRegistrationUrl(): string
    {
        return route('discord.register');
    }

    public static function getGuildId(): ?string
    {
        return self::getSettings()->getValue('guild_id');
    }

    public static function isTribeSetup(): bool
    {

        return !!self::getSettings()->getValue('guild_id');
    }

    public static function getConnectorConfigUrl(): string
    {
        // get the vendor name from the composer
        return "https://github.com/seatplus/discord#package_description";
    }

    public static function isConnectorConfigured(): bool
    {
        $config = config('services.discord');

        // check if any of the config values are null
        return !in_array(null, $config);
    }

    public static function isTribeEnabled(): bool
    {
        return !!self::getSettings()->getValue('tribes');
    }

    public static function enableTribe(): void
    {
        self::getSettings()->setValue('tribes', true);
    }

    public static function disableTribe(): void
    {
        self::getSettings()->setValue('tribes', false);
    }

    public static function getTribeSettings(): array
    {
        return self::getSettings()->getValue('tribe_settings') ?? [];
    }

    public static function setTribeSettings(array $settings): void
    {
        self::getSettings()->setValue('tribe_settings', $settings);
    }

    public static function getSettings(): Settings
    {
        return Settings::firstOrCreate(['connector' => Discord::class]);
    }


    public static function getNicknamePolicyCommandImplementation(): string
    {
        return NicknameCommand::class;
    }

    public static function getSquadSyncCommandImplementation(): string
    {
        return SquadSyncCommand::class;
    }
}
