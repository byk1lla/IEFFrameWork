<?php
/**
 * GuestMiddleware — zaten giriş yapmış kullanıcıyı /admin'e yönlendirir.
 *
 * Login/Register sayfaları için kullanılır.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;

class GuestMiddleware
{
    public function handle(Request $request, array $params = []): bool
    {
        if (Auth::check()) {
            header('Location: /admin');
            return false;
        }
        return true;
    }
}
