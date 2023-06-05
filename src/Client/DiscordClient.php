<?php

namespace Seatplus\Discord\Client;

use Composer\InstalledVersions;
use GuzzleHttp\Client;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use OutOfBoundsException;
use Seatplus\Connector\Models\User;
use Seatplus\Discord\Discord;

abstract class DiscordClient
{
    protected PendingRequest $client;

    public function getClient(): PendingRequest
    {
        if(! isset($this->client)) {

            // get installed version of this package via composer
            try {
                $version = InstalledVersions::getPrettyVersion('seatplus/discord');
            } catch (OutOfBoundsException $e) {
                $version = 'dev';
            }

            $this->client = Http::baseUrl('https://discord.com/api/')
                ->withHeaders([
                    'Authorization' => 'Bot ' . config('services.discord.bot_token'),
                    'User-Agent' => "Seatplus Discord Connector v.${version}"
                ])
                ->acceptJson();
        }

        return $this->client;
    }

    /**
     * @throws \Throwable
     */
    public function invoke(string $method, string $endpoint, array $url_parameters, array $options = []): Response
    {
        $response = $this->getClient()
            ->withUrlParameters($url_parameters)
            ->$method($endpoint, $options);

        if($response->clientError()) {

            // get body from response
            $code = $response->json('code');

            match ($code) {
                10007 => $this->deleteUser($url_parameters),
                10004 => $this->deleteGuild()
            };
        }

        return $response->throw();
    }

    /**
     * @throws \Throwable
     */
    private function deleteUser(array $url_parameters): void
    {

        // get user.id from url parameters
        $user_id = $url_parameters['user.id'];

        // if user_id is not set, throw exception
        throw_unless($user_id, new \Exception('User id is not set'));

        // get all users
        $users = Discord::users();

        // find user
        $user = $users->firstWhere('connector_id', $user_id);

        // delete user
        $user->delete();
    }

    private function deleteGuild(): void
    {
        Discord::getSettings()->setValue('guild_id', null);
    }



}
