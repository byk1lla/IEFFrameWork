<?php
/**
 * AuthMiddleware — yetkisiz isteği /login'e redirect eder.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request, array $params = []): bool
    {
        if (Auth::check()) {
            // Param destekli kısıtlama:
            //   'Auth:admin'          → role kontrol
            //   'Auth:perm:blog.edit' → permission kontrol (can() helper'ı)
            if ($params) {
                $user = Auth::user();
                $role = $user?->role ?? 'user';

                foreach ($params as $p) {
                    if (str_starts_with($p, 'perm:')) {
                        if (!can(substr($p, 5))) {
                            http_response_code(403);
                            echo '403 Forbidden — yetki yok';
                            return false;
                        }
                    }
                }
                // Role-bazlı param varsa (perm: olmayan)
                $rolesOnly = array_values(array_filter($params, fn ($p) => !str_starts_with($p, 'perm:')));
                if ($rolesOnly && !in_array($role, $rolesOnly, true) && $role !== 'superadmin') {
                    http_response_code(403);
                    echo '403 Forbidden — bu role bu sayfayı görüntüleyemez';
                    return false;
                }
            }
            return true;
        }

        // Erişilmek istenen URL'i flash'a koy ki login sonrası geri dönülebilsin
        Session::flash('intended_url', $request->fullUrl());

        if ($request->isAjax() || $request->isJson()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            return false;
        }

        header('Location: /login');
        return false;
    }
}
