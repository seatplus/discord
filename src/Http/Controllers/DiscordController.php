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

        return $provider->redirect();
    }


    public function callback(HandleSocialiteCallbackAction $handle_socialite_callback_action)
    {
        $handle_socialite_callback_action->execute();

        return redirect()->route('tribe.index');
    }



}
