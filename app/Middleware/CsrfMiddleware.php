<?php
/**
 * CsrfMiddleware — POST/PUT/PATCH/DELETE isteklerinde CSRF token doğrular.
 *
 * Header (X-CSRF-TOKEN) veya form field (_csrf_token) destekler.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class CsrfMiddleware
{
    public function handle(Request $request, array $params = []): bool
    {
        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }
        $token = $request->input('_csrf_token') ?? $request->header('X-CSRF-TOKEN');
        if (!is_string($token) || !Session::verifyCsrfToken($token)) {
            Response::abort(419);
            return false;
        }
        return true;
    }
}
