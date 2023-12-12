<?php

namespace Seatplus\Discord\Http\Controllers;

use Illuminate\Http\Request;
use Seatplus\Discord\Http\Actions\GetSocialiteProviderAction;
use Seatplus\Discord\Http\Actions\HandleSocialiteCallbackAction;

final class DiscordController
{

    public function register(GetSocialiteProviderAction $get_socialite_provider_action)
    {
        $provider = $get_socialite_provider_action->execute();

        // get previous route
        $previous_route = url()->previous();

        //store previous route in session
        session(['previous_route' => $previous_route]);

        return $provider->redirect();
    }


    public function callback(HandleSocialiteCallbackAction $handle_socialite_callback_action)
    {
        $handle_socialite_callback_action->execute();

        // get and delete previous route from session
        $previous_route = session()->pull('previous_route');

        // if previous route is set redirect to it
        if($previous_route) {
            return redirect($previous_route);
        }

        return redirect()->route('tribe.index');
    }



}
