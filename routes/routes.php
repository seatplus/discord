<?php

use Illuminate\Support\Facades\Route;
use Seatplus\Auth\Http\Middleware\CheckPermissionOrCorporationRole;

Route::prefix('discord')
    ->middleware(['web', 'auth', CheckPermissionOrCorporationRole::class.':view tribes'])
    ->controller(\Seatplus\Discord\Http\Controllers\DiscordController::class)
    ->group(function () {
        Route::get('/register', 'register')->name('discord.register');
        Route::get('/callback', 'callback')->name('discord.callback');

    });
