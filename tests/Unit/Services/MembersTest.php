<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Seatplus\Discord\Discord;

beforeEach(function () {

    // set guild_id and user_id

    // create a binding scoped singleton
    $this->guild_id = '123456789';
    app()->scoped(\Seatplus\Discord\Client\Guild::class, fn () => new \Seatplus\Discord\Client\Guild($this->guild_id));

    $this->user_id = '1';

});

it('gets Members Role', function () {

    // fake http response
    Http::fake(fn () => Http::response(test()->getDiscordMember()));

    $member = new \Seatplus\Discord\Services\Members\GetMemberAttribute($this->user_id);

    expect($member->roles())->toBeArray();

    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://discord.com/api/guilds/123456789/members/1';
    });

});

it('gets Members Nick', function () {

    // fake http response
    Http::fake(fn () => Http::response(test()->getDiscordMember()));

    $member = new \Seatplus\Discord\Services\Members\GetMemberAttribute($this->user_id);

    expect($member->nick())->toBeString();

    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://discord.com/api/guilds/123456789/members/1';
    });

});

it('updates users nick', function (?string $nick_pre_fix, ?string $suffix, bool $ticker, string $current_nick) {

    Http::fake(fn () => Http::response([...test()->getDiscordMember(), 'nick' => $current_nick]));

    // set prefix, suffix and ticker
    Discord::getSettings()->getValue('guild_id'); // getting it before setting it to make sure it is not set
    Discord::getSettings()->setValue('prefix', $nick_pre_fix);
    Discord::getSettings()->setValue('suffix', $suffix);
    Discord::getSettings()->setValue('ticker', $ticker);

    expect(Discord::getSettings()->settings)->toHaveCount(3);

    // create new User
    $user = Event::fakeFor(fn () => \Seatplus\Auth\Models\User::factory()->create());

    // create new connector_user
    \Seatplus\Connector\Models\User::create([
        'user_id' => $user->id,
        'connector_id' => $this->user_id,
        'connector_type' => Discord::class,
    ]);

    // expect user to have a ticker
    expect($user->main_character->corporation->ticker)->toBeString();

    // expect to have 1 DiscordUser
    expect(Discord::users())->toHaveCount(1);

    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', $this->guild_id);

    $update_action = new \Seatplus\Discord\Services\Members\UpdateUsersNick;

    $update_action->execute();

    // assert that 2 requests are sent (getMember and updateMember)
    Http::assertSentCount(2);

    // get the second request as this is the one doing the update
    [$request, $response] = Http::recorded()[1];

    // assert that the request method is a patch
    expect($request->method())->toBe('PATCH');

    // create the expected strings array
    $expected_strings = [$nick_pre_fix, $current_nick, $suffix];

    if ($ticker) {
        $expected_strings[] = $user->main_character->corporation->ticker;
    }

    // filter out null values
    $expected_strings = array_filter($expected_strings);

    // assert that the nick is set correctly
    expect(Str::containsAll($request['nick'], $expected_strings))->toBeTrue();

})->with([
    [
        'nick_pre_fix' => 'prefix',
        'suffix' => 'suffix',
        'ticker' => true,
        'current_nick' => 'test',
    ],
    [
        'nick_pre_fix' => 'nick_pre_fix',
        'suffix' => 'suffix',
        'ticker' => false,
        'current_nick' => 'test',
    ],
    [
        'nick_pre_fix' => null,
        'suffix' => 'suffix',
        'ticker' => true,
        'current_nick' => 'test',
    ],
    [
        'nick_pre_fix' => null,
        'suffix' => 'suffix',
        'ticker' => false,
        'current_nick' => 'test',
    ],
    [
        'nick_pre_fix' => 'nick_pre_fix',
        'suffix' => null,
        'ticker' => true,
        'current_nick' => 'test',
    ],
    [
        'nick_pre_fix' => 'nick_pre_fix',
        'suffix' => null,
        'ticker' => false,
        'current_nick' => 'test',
    ],
    [
        'nick_pre_fix' => null,
        'suffix' => null,
        'ticker' => true,
        'current_nick' => 'test',
    ],
]);

it('does not update member if no prefix, no suffic, no ticker is set', function () {

    Http::fake(fn () => Http::response([...test()->getDiscordMember(), 'nick' => 'test']));

    // set prefix, suffix and ticker
    Discord::getSettings()->getValue('guild_id'); // getting it before setting it to make sure it is not set
    Discord::getSettings()->setValue('prefix', null);
    Discord::getSettings()->setValue('suffix', null);
    Discord::getSettings()->setValue('ticker', false);

    // create new User
    $user = Event::fakeFor(fn () => \Seatplus\Auth\Models\User::factory()->create());

    // create new connector_user
    \Seatplus\Connector\Models\User::create([
        'user_id' => $user->id,
        'connector_id' => $this->user_id,
        'connector_type' => Discord::class,
    ]);

    // expect user to have a ticker
    expect($user->main_character->corporation->ticker)->toBeString();

    // expect to have 1 DiscordUser
    expect(Discord::users())->toHaveCount(1);

    Discord::getSettings()->getValue('guild_id');
    Discord::getSettings()->setValue('guild_id', $this->guild_id);

    $update_action = new \Seatplus\Discord\Services\Members\UpdateUsersNick();

    $update_action->execute();

    // assert that 1 request are sent (getMember)
    Http::assertSentCount(1);

    Http::assertSent(function (Request $request) {
        return $request->url() === 'https://discord.com/api/guilds/123456789/members/1';
    });
});
