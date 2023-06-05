<?php

namespace Seatplus\Discord\Http\Actions;

use Laravel\Socialite\Facades\Socialite;
use Seatplus\Connector\Models\Settings;
use Seatplus\Connector\Models\User;
use Seatplus\Discord\Client\Member;
use Seatplus\Discord\Discord;

class HandleSocialiteCallbackAction
{
    public ?string $guild_id;

    public function __construct()
    {
        $this->guild_id = Discord::getGuildId();
    }

    public function execute(): void
    {

        /** @var \SocialiteProviders\Manager\OAuth2\User $socialite_user */
        $socialite_user = Socialite::driver('discord')->user();

        if(!$this->guild_id) {
            $this->persistGuildInformation($socialite_user);
        }

        $user = User::query()
            ->where('user_id', auth()->user()->getAuthIdentifier())
            ->where('connector_type', Discord::class)
            ->first();

        // if user is already registered remove the old user
        if($user && $user->connector_id !== $socialite_user->getId()) {
            $this->kickUser($user);
        }

        User::firstOrCreate([
            'user_id' => auth()->user()->getAuthIdentifier(),
        ], [
            'connector_id' => $socialite_user->getId(),
            'connector_type' => Discord::class,
        ]);
    }

    private function persistGuildInformation(\SocialiteProviders\Manager\OAuth2\User $socialite_user)
    {
        $accessTokenResponseBody = $socialite_user->accessTokenResponseBody;

        Settings::updateOrCreate([
            'connector' => Discord::class,
        ], [
            'settings' => [
                'guild_id' => data_get($accessTokenResponseBody, 'guild.id'),
                'owner_id' => data_get($accessTokenResponseBody, 'guild.owner_id')
            ],
        ]);
    }

    private function kickUser(User $user)
    {
        $client = new Member($this->guild_id, $user->connector_id);
        $client->delete();
    }

}
