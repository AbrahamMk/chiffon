<?php

namespace App\Support;

use App\Models\User;

class AuthRedirect
{
    public static function home(?User $user = null): string
    {
        $user ??= auth()->user();

        if ($user && $user->isStaff()) {
            return route('admin.dashboard', absolute: false);
        }

        return route('home', absolute: false);
    }
}
