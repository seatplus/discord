<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Seatplus\Discord\Tests\TestCase;

/*uses(TestCase::class)
    ->group('integration')
    ->in('Integration');*/

uses(TestCase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function getDiscordMember(): array
{
    return [
        'id' => '1',
        'nick' => 'test',
        'discriminator' => '1234',
        'avatar' => '1234',
        'bot' => false,
        'system' => false,
        'mfa_enabled' => false,
        'locale' => 'en-US',
        'verified' => false,
        'roles' => [
            '10',
            '20',
            '30',
        ],
    ];

}

function getDiscordRolesMock()
{
    return [
        [
            'id' => '10',
            'name' => 'role1',
            'color' => 0,
            'hoist' => false,
            'position' => 1,
            'permissions' => 0,
            'managed' => false,
            'mentionable' => false,
        ],
        [
            'id' => '20',
            'name' => 'role2',
            'color' => 0,
            'hoist' => false,
            'position' => 2,
            'permissions' => 0,
            'managed' => false,
            'mentionable' => false,
        ],
        [
            'id' => '30',
            'name' => 'role3',
            'color' => 0,
            'hoist' => false,
            'position' => 3,
            'permissions' => 0,
            'managed' => false,
            'mentionable' => false,
        ],
    ];
}
