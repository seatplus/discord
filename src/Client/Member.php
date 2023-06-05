<?php

namespace Seatplus\Discord\Client;

use Illuminate\Http\Client\RequestException;
class Member extends DiscordClient
{
    const ENDPOINT = 'guilds/{guild.id}/members/{user.id}';
    public function __construct(
        public int $guild_id,
        public int $user_id
    )
    {
    }

    /**
     * @throws RequestException|\Throwable
     */
    public function get(string $key = null): array|string|bool|int|null
    {
        $response =  $this->invoke('GET', self::ENDPOINT, [
            'guild.id' => $this->guild_id,
            'user.id' => $this->user_id,
        ]);

        return $response->json($key);

    }

    public function update(array $data): void
    {
        $this->invoke('PATCH', self::ENDPOINT, [
            'guild.id' => $this->guild_id,
            'user.id' => $this->user_id,
        ], $data);
    }

    public function delete(): void
    {
        $this->invoke('DELETE', self::ENDPOINT, [
            'guild.id' => $this->guild_id,
            'user.id' => $this->user_id,
        ]);
    }



}
