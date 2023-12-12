<?php

namespace Seatplus\Discord;

use Seatplus\BroadcastHub\Contracts\Broadcaster;
use Seatplus\BroadcastHub\Contracts\Notification;
use Seatplus\BroadcastHub\Recipient;
use Seatplus\Connector\Models\Settings;
use Seatplus\Connector\Models\User;
use Seatplus\Discord\Client\Guild;
use Seatplus\Discord\Client\User as DiscordUser;
use Seatplus\Discord\Commands\NicknameCommand;
use Seatplus\Discord\Commands\SquadSyncCommand;
use Seatplus\Discord\Notifications\ErrorNotification;
use Seatplus\Discord\Notifications\NewCorporationMember;
use Seatplus\Tribe\Contracts\Tribe;

class Discord implements Broadcaster, Tribe
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

        return (bool) self::getSettings()->getValue('guild_id');
    }

    public static function getConnectorConfigUrl(): string
    {
        // get the vendor name from the composer
        return 'https://github.com/seatplus/discord#package_description';
    }

    public static function isConnectorConfigured(): bool
    {
        $config = config('services.discord');

        // check if any of the config values are null
        return ! in_array(null, $config);
    }

    public static function isTribeEnabled(): bool
    {
        return (bool) self::getSettings()->getValue('tribes');
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

    public static function getNotifiableId(Recipient $subscriber): ?string
    {

        // if $recipient has a name return the id as this is the discord channel id
        if ($subscriber->name) {
            return $subscriber->connector_id;
        }

        return app(DiscordUser::class)->getPrivateChannel($subscriber->connector_id);
    }

    public static function getImplementedNotificationClasses(): array
    {

        return [
            NewCorporationMember::class,
            ErrorNotification::class,
        ];
    }

    public static function isBroadcasterEnabled(): bool
    {
        return self::getSettings()->getValue('broadcaster.enabled', false);
    }

    public static function getEnablePermission(): string
    {
        return 'superuser';
    }

    public static function userCanEnableBroadcaster(): bool
    {
        return auth()->user()->can(self::getEnablePermission());
    }

    public static function isConnectorSetup(): bool
    {
        return (bool) self::getSettings()->getValue('guild_id');
    }

    public static function storeBroadcasterSettings(array $settings): void
    {
        self::getSettings()->setValue('broadcaster', $settings);
    }

    /**
     * @throws \Exception
     */
    public static function findImplementedNotificationClass(string $notification_class): string
    {
        // find the notification class in the implemented notification classes that extends the notification class
        $implemented_notification_classes = self::getImplementedNotificationClasses();

        $notification_class = collect($implemented_notification_classes)
            ->first(fn ($class) => is_subclass_of($class, $notification_class));

        // throw an exception if the notification class is not found
        if (! $notification_class) {
            throw new \Exception('Notification not implemented');
        }

        return $notification_class;

    }

    public static function getChannels(): array
    {

        $channels = app(Guild::class)->getGuildChannels();

        // filter out the channels that are not text channels by flags
        return collect($channels)
            ->filter(fn ($channel) => $channel['type'] === 0)
            ->map(fn ($channel) => [
                'id' => $channel['id'],
                'name' => $channel['name'],
            ])
            ->toArray();

    }
}
