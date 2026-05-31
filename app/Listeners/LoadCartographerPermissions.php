<?php

namespace App\Listeners;

use App\Models\Cartographer;
use Illuminate\Auth\Events\Login;

class LoadCartographerPermissions
{
    public function handle(Login $event): void
    {
        Cartographer::loadSessionFor($event->user);
    }
}
