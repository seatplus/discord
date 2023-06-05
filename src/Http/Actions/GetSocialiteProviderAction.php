<?php

namespace Seatplus\Discord\Http\Actions;

use Seatplus\Discord\Discord;
use SocialiteProviders\Discord\Provider;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use Laravel\Socialite\Facades\Socialite;

class GetSocialiteProviderAction
{

    const BOT_SCOPES = [
        'identify',
        'guilds.join',
        'bot',
    ];

    const SCOPES = [
        'identify',
        'guilds.join',
    ];

    // https://discord.com/developers/docs/topics/permissions#permissions-bitwise-permission-flags
    const BOT_PERMISSIONS = [
        'MANAGE_ROLES'          => 0x10000000,
        'KICK_MEMBERS'          => 0x00000002,
        'BAN_MEMBERS'           => 0x00000004,
        'CREATE_INSTANT_INVITE' => 0x00000001,
        'CHANGE_NICKNAME'       => 0x04000000,
        'MANAGE_NICKNAMES'      => 0x08000000,
        'SEND_MESSAGES'         => 0x00000800,
    ];

    private ?string $guild_id;

    public function __construct()
    {
        $this->guild_id = Discord::getGuildId();
    }

    public function execute() : AbstractProvider
    {

        /** @var Provider $socialite */
        $socialite = Socialite::driver('discord');

        $scopes = match ($this->guild_id) {
            null => self::BOT_SCOPES,
            default => self::SCOPES,
        };

        $socialite = $socialite->setScopes($scopes);

        if(!$this->guild_id) {
            $socialite = $socialite->with(['permissions' => $this->getBotPermissions()]);
        }

        return $socialite;
    }

    // bot permissions are calculated by bitwise OR
    private function getBotPermissions(): int
    {
        $permissions = 0;

        // The | (bitwise inclusive OR) operator compares the values (in binary format) of each operand and yields a
        // value whose bit pattern shows which bits in either of the operands has the value 1.
        // If both of the bits are 0, the result of that bit is 0; otherwise, the result is 1.
        // see: https://www.ibm.com/docs/en/i/7.2?topic=expressions-bitwise-inclusive-operator
        foreach (self::BOT_PERMISSIONS as $permission) {
            $permissions |= $permission;
        }

        return $permissions;
    }
}
