<?php

namespace Seatplus\Discord\Client;

use Illuminate\Support\Collection;
use Seatplus\Discord\Discord;

class Guild
{
    const CHANNEL_ENDPOINT = 'guilds/{guild.id}/channels';

    const MEMBER_ENDPOINT = 'guilds/{guild.id}/members/{user.id}';

    const ROLE_ENDPOINT = 'guilds/{guild.id}/roles';

    public string $guild_id;

    private DiscordClient $client;

    public function __construct(
        ?string $guild_id = null
    ) {
        $this->client = new DiscordClient();

        if ($guild_id) {
            $this->setGuildId($guild_id);
        }
    }

    public function getGuildChannels(?string $key = null): array|string|bool|int|null
    {
        return $this->client->invoke('GET', self::CHANNEL_ENDPOINT, [
            'guild.id' => $this->getGuildId(),
        ])->json($key);
    }

    public function getGuildMember(string $user_id, ?string $key = null): array|string|bool|int|null
    {
        return $this->client->invoke('GET', self::MEMBER_ENDPOINT, [
            'guild.id' => $this->getGuildId(),
            'user.id' => $user_id,
        ])->json($key);
    }

    public function modifyGuildMember(string $user_id, array $data): void
    {
        $this->client->invoke('PATCH', self::MEMBER_ENDPOINT, [
            'guild.id' => $this->getGuildId(),
            'user.id' => $user_id,
        ], $data);
    }

    public function removeGuildMember(string $user_id): void
    {
        $this->client->invoke('DELETE', self::MEMBER_ENDPOINT, [
            'guild.id' => $this->getGuildId(),
            'user.id' => $user_id,
        ]);
    }

    public function getGuildRoles(?string $id = null): Collection|array
    {
        $roles = $this->client->invoke('GET', self::ROLE_ENDPOINT, [
            'guild.id' => $this->getGuildId(),
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
            'guild.id' => $this->getGuildId(),
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
                'guild.id' => $this->getGuildId(),
            ],
            [
                ['id' => $role_id, 'position' => $position],
            ])->collect();
    }

    private function getGuildId(): string
    {
        if (! isset($this->guild_id)) {
            $this->guild_id = Discord::getGuildId();
        }

        return $this->guild_id;
    }

    public function setGuildId(string $guild_id): void
    {
        $this->guild_id = $guild_id;
    }
}
