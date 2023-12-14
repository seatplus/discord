<?php

use Seatplus\Discord\Http\Actions\GetSocialiteProviderAction;
use Seatplus\Discord\Http\Actions\HandleSocialiteCallbackAction;
use Seatplus\Discord\Http\Controllers\DiscordController;

it('tests register method', function () {

    $action = $this->mock(GetSocialiteProviderAction::class, function ($mock) {

        $abstract_provider_mock = $this->mock(\SocialiteProviders\Manager\OAuth2\AbstractProvider::class, function ($mock) {
            $mock->shouldReceive('redirect')
                ->once()
                ->andReturn('redirect');
        });

        $mock->shouldReceive('execute')
            ->once()
            ->andReturn($abstract_provider_mock);
    });

    $controller = new DiscordController();
    $response = $controller->register($action);

    expect($response)->toBe('redirect');
});

it('tests callback method', function (?string $previous_route) {

    $action = $this->mock(HandleSocialiteCallbackAction::class, function ($mock) {
        $mock->shouldReceive('execute')
            ->once();
    });

    // set previous route
    session(['previous_route' => $previous_route]);

    $controller = new DiscordController();
    $response = $controller->callback($action);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);

    if ($previous_route) {
        expect($response->getTargetUrl())->toBe('http://localhost/'.$previous_route);
    } else {
        expect($response->getTargetUrl())->toBe(route('tribe.index'));
    }
})->with([null, 'previous_route']);
