<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Auth;

class AuthMiddleware
{
    public function handle(Request $request, array $params = []): bool
    {
        // Debug session
        if (!Auth::check()) {
            header("X-Auth-Redirect: YES");
            redirect('/login');
            return false;
        }

        return true;
    }
}
