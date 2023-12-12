<?php

namespace Seatplus\Discord\Client;

use Illuminate\Support\Collection;

class Guild
{
    const CHANNEL_ENDPOINT = 'guilds/{guild.id}/channels';

    const MEMBER_ENDPOINT = 'guilds/{guild.id}/members/{user.id}';

    const ROLE_ENDPOINT = 'guilds/{guild.id}/roles';

    public function __construct(
        public int $guild_id,
        private ?DiscordClient $client = null
    ) {
        if (! $this->client) {
            $this->client = app(DiscordClient::class);
        }
    }

    public function getGuildChannels(?string $key = null): array|string|bool|int|null
    {
        return $this->client->invoke('GET', self::CHANNEL_ENDPOINT, [
            'guild.id' => $this->guild_id,
        ])->json($key);
    }

    public function getGuildMember(string $user_id, ?string $key = null): array|string|bool|int|null
    {
        return $this->client->invoke('GET', self::MEMBER_ENDPOINT, [
            'guild.id' => $this->guild_id,
            'user.id' => $user_id,
        ])->json($key);
    }

    public function modifyGuildMember(string $user_id, array $data): void
    {
        $this->client->invoke('PATCH', self::MEMBER_ENDPOINT, [
            'guild.id' => $this->guild_id,
            'user.id' => $user_id,
        ], $data);
    }

    public function removeGuildMember(string $user_id): void
    {
        $this->client->invoke('DELETE', self::MEMBER_ENDPOINT, [
            'guild.id' => $this->guild_id,
            'user.id' => $user_id,
        ]);
    }

    public function getGuildRoles(?string $id = null): Collection|array
    {
        $roles = $this->client->invoke('GET', self::ROLE_ENDPOINT, [
            'guild.id' => $this->guild_id,
        ])
            ->collect()
            ->sortByDesc('position');

        if ($id) {
            return $roles->firstWhere('id', $id);
        }

        return $roles
            ->filter(fn (array $role) => $role['name'] !== '@everyone')
            ->filter(fn (array $role) => ! $role['managed']);
    }

    public function createGuildRole(string $name, ?int $position = null): array
    {
        $role = $this->client->invoke('POST', self::ROLE_ENDPOINT, [
            'guild.id' => $this->guild_id,
        ], [
            'name' => $name,
        ]);

        if ($position) {
            $this->modifyGuildRolePositions($role['id'], $position);
        }

        return $role->json();
    }

    public function modifyGuildRolePositions(string $role_id, int $position): Collection
    {
        return $this->client->invoke('PATCH', self::ROLE_ENDPOINT,
            [
                'guild.id' => $this->guild_id,
            ],
            [
                ['id' => $role_id, 'position' => $position],
            ])->collect();
    }
}
