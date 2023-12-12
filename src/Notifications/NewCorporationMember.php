<?php

namespace Seatplus\Discord\Notifications;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use NumberFormatter;

class NewCorporationMember extends \Seatplus\BroadcastHub\Notifications\NewCorporationMember
{

    public function via() : array
    {
        return [DiscordChannel::class];
    }

    public function toBroadcaster(): DiscordMessage
    {

        $corporation_name = $this->corporation->name;

        // get the number of new corporation members
        $new_corporation_members_count = count($this->new_corporation_members);

        // format the number to a string
        $$new_corporation_members_count = (new NumberFormatter('en_US', NumberFormatter::SPELLOUT))->format($new_corporation_members_count);

        $pluralized_member = Str::plural('member', $new_corporation_members_count);

        $message = DiscordMessage::create("New corporation {$pluralized_member} ({$new_corporation_members_count}) in {$corporation_name} ");

        // add an embed to the message for every new corporation member
        foreach ($this->new_corporation_members as $new_corporation_member) {
            $message = $message->addEmbed([
                'title' => $new_corporation_member['character']['name'],
                'description' => "Joined {$this->getHumanTime($new_corporation_member['start_date'])}",
                'thumbnail' => ['url' => "https://images.evetech.net/characters/{$new_corporation_member['character']['character_id']}/portrait"],
            ]);
        }

        return $message;
    }

    private function getHumanTime(string $date): string
    {
        return Carbon::parse($date)->diffForHumans();
    }
}
