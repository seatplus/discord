<?php

namespace Seatplus\Discord\Services\Members;

use Illuminate\Support\Collection;
use Seatplus\Connector\Models\User;
use Seatplus\Discord\Discord;

class UpdateUsersNick
{
    private ?string $guild_id;
    private Collection $users;

    public function __construct(
        private ?string $prefix = null,
        private ?string $suffix = null,
        private bool $has_ticker = false,
        private bool $has_error = false,
    )
    {
        $this->guild_id = Discord::getGuildId();
        $this->users = Discord::users();

        $settings = Discord::getSettings();

        $this->prefix = $settings->getValue('prefix');
        $this->suffix = $settings->getValue('suffix');

        $this->has_ticker = $settings->getValue('ticker') ?? false;
    }

    public function execute()
    {

        $this->users->each(fn (User $user) => $this->handleUser($user));
    }

    private function handleUser(User $user): void
    {

        try {
            $ticker = null;

            if($this->has_ticker) {
                /** @phpstan-ignore-next-line */
                $ticker = $user->seatplusUser->main_character->corporation->ticker;
            }

            $action = (new ApplyNickPreAndPostFixToMember($user->connector_id));

            $action->execute(
                nick_pre_fix: $this->prefix,
                suffix: $this->suffix,
                ticker: $ticker
            );
        } catch (\Exception $e) {
            // log the exception
            report($e);

            $this->setHasError();

            // skip the user
            return;
        }


    }

    public function hasError(): bool
    {
        return $this->has_error;
    }

    public function setHasError(): void
    {
        $this->has_error = true;
    }

}
