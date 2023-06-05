<?php

namespace Seatplus\Discord\Client;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

class Roles extends DiscordClient
{
    const ENDPOINT = 'guilds/{guild.id}/roles';

    public function __construct(
        public int $guild_id
    )
    {
    }

    public function get(string $id = ''): Collection|array
    {
        $roles = $this->get_raw();

        if($id) {
            return $roles->firstWhere('id', $id);
        }

        return $roles
            ->filter(fn (array $role) => $role['name'] !== '@everyone')
            ->filter(fn (array $role) => !$role['managed']);
    }

    public function get_raw(): Collection
    {
        return $this->invoke('GET', self::ENDPOINT, [
            'guild.id' => $this->guild_id,
        ])
            ->collect()
            ->sortByDesc('position');
    }

    public function create(string $name, ?int $position = null)
    {
        $role = $this->invoke('POST', self::ENDPOINT, [
            'guild.id' => $this->guild_id,
        ], [
            'name' => $name,
        ]);

        if($position) {
            $this->update($role['id'], $position);
        }

        return $role->json();
    }

    public function update($id, int $position): Collection
    {

        return $this->invoke('PATCH', self::ENDPOINT, [
            'guild.id' => $this->guild_id,

        ], [
            ['id' => $id, 'position' => $position]
        ])->collect();
    }

}
