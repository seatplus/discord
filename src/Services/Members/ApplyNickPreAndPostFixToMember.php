<?php

namespace Seatplus\Discord\Services\Members;

use Illuminate\Support\Str;

class ApplyNickPreAndPostFixToMember
{
    private UpdateMemberAttribute $update_member_attribute;

    private GetMemberAttribute $get_member_attribute;

    public function __construct($user_id)
    {

        $this->update_member_attribute = new UpdateMemberAttribute($user_id);
        $this->get_member_attribute = new GetMemberAttribute($user_id);
    }

    public function execute(?string $nick_pre_fix = null, ?string $suffix = null, ?string $ticker = null): void
    {

        // get current nick
        $current_nick = $this->get_member_attribute->nick();
        $new_nick = $current_nick;

        // if nick_pre_fix is not null or ticker is not null
        if ($nick_pre_fix || $ticker) {

            $starts_with = match (true) {
                // if nick_pre_fix is null and ticker is not null
                $nick_pre_fix === null && $ticker !== null => "[{$ticker}]",
                // if nick_pre_fix is not null and ticker is null
                $nick_pre_fix !== null && $ticker === null => $nick_pre_fix,
                // if nick_pre_fix is not null and ticker is not null
                $nick_pre_fix !== null && $ticker !== null => "[{$ticker} - {$nick_pre_fix}]",
                default => null
            };

            // if current nick does not start with starts_with, add it
            if (! Str::startsWith($new_nick, $starts_with)) {
                $new_nick = "{$starts_with} {$new_nick}";
            }
        }

        // if nick_post_fix is not null
        if ($suffix) {
            // if current nick does not end with suffix, add it
            if (! Str::endsWith($new_nick, $suffix)) {
                $new_nick = "{$new_nick} {$suffix}";
            }
        }

        // if current nick is not equal to new nick, update it
        if ($current_nick !== $new_nick) {
            $this->update_member_attribute->nick($new_nick);
        }

    }
}
