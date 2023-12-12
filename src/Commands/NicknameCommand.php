<?php

namespace Seatplus\Discord\Commands;

use Illuminate\Console\Command;
use Seatplus\Discord\Services\Members\UpdateUsersNick;

class NicknameCommand extends Command
{
    public $signature = 'tribe:nickname:discord';

    public $description = 'Applies nickname rules for discord members';

    public function handle(UpdateUsersNick $update_users_nick): int
    {
        $update_users_nick->execute();

        if ($update_users_nick->hasError()) {
            $this->error('There was an error updating the nicknames, check the logs!');

            return self::FAILURE;
        } else {
            $this->info('Nicknames updated');
        }

        return self::SUCCESS;

    }
}
