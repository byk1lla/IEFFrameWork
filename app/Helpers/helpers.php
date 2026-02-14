<?php
/**
 * Global Helper Fonksiyonları
 * 
 * @package    IEF Framework
 */

use App\Core\Config;
use App\Core\Session;

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, $default = null)
    {
        if ($key === null) {
            return Session::class;
        }
        return Session::get($key, $default);
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return Session::user();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Session::getCsrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('method_field')) {
    function method_field(string $method): string
    {
        return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        return Session::getFlash('old_input')[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, $default = null)
    {
        return Session::getFlash($key, $default);
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $baseUrl = rtrim(config('app.url', ''), '/');
        return $baseUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}

if (!function_exists('back')) {
    function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('h')) {
    function h(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('dd')) {
    function dd(...$vars): void
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit;
    }
}

if (!function_exists('dump')) {
    function dump(...$vars): void
    {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
    }
}

if (!function_exists('now')) {
    function now(): \Carbon\Carbon
    {
        return \Carbon\Carbon::now('Europe/Istanbul');
    }
}

if (!function_exists('uuid')) {
    function uuid(): string
    {
        return \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
    }
}

if (!function_exists('format_money')) {
    function format_money($amount, string $currency = '₺'): string
    {
        return number_format((float) $amount, 2, ',', '.') . ' ' . $currency;
    }
}

if (!function_exists('format_date')) {
    function format_date($date, string $format = 'd.m.Y'): string
    {
        if (empty($date))
            return '-';
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime($date, string $format = 'd.m.Y H:i'): string
    {
        if (empty($date))
            return '-';
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('format_phone')) {
    function format_phone(?string $phone): string
    {
        if (empty($phone))
            return '-';
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($cleaned) === 10) {
            return sprintf(
                '(%s) %s %s %s',
                substr($cleaned, 0, 3),
                substr($cleaned, 3, 3),
                substr($cleaned, 6, 2),
                substr($cleaned, 8, 2)
            );
        }
        return $phone;
    }
}

if (!function_exists('str_limit')) {
    function str_limit(?string $value, int $limit = 100, string $end = '...'): string
    {
        if (empty($value))
            return '';
        if (mb_strlen($value) <= $limit)
            return $value;
        return mb_substr($value, 0, $limit) . $end;
    }
}

if (!function_exists('class_active')) {
    function class_active(string $path, string $class = 'active'): string
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $currentPath === $path ? $class : '';
    }
}

if (!function_exists('is_current_route')) {
    function is_current_route(string $path): bool
    {
        $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $currentPath === $path || str_starts_with($currentPath, $path . '/');
    }
}

if (!function_exists('status_badge')) {
    function status_badge(string $status): string
    {
        $config = require CONFIG_PATH . '/app.php';
        $statuses = $config['order_statuses'] ?? [];
        $statusConfig = $statuses[$status] ?? [];

        $name = $statusConfig['name'] ?? $status;
        $color = $statusConfig['color'] ?? 'gray';

        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-%s-100 text--%s-800">%s</span>',
            $color,
            $color,
            e($name)
        );
    }
}

if (!function_exists('role_badge')) {
    function role_badge(string $role): string
    {
        $config = require CONFIG_PATH . '/app.php';
        $roles = $config['roles'] ?? [];
        $roleConfig = $roles[$role] ?? [];

        $name = $roleConfig['name'] ?? $role;
        $colors = [
            'superadmin' => 'red',
            'admin' => 'purple',
            'personel' => 'blue',
            'driver' => 'green',
        ];
        $color = $colors[$role] ?? 'gray';

        return sprintf(
            '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-%s-100 text-%s-800">%s</span>',
            $color,
            $color,
            e($name)
        );
    }
}

if (!function_exists('can')) {
    function can(string $permission): bool
    {
        $user = auth();
        if (!$user)
            return false;

        $role = $user['role'];
        if ($role === 'superadmin')
            return true;

        $config = require CONFIG_PATH . '/app.php';
        $roleConfig = $config['roles'][$role] ?? [];
        $permissions = $roleConfig['permissions'] ?? [];

        if (in_array('*', $permissions))
            return true;
        if (in_array($permission, $permissions))
            return true;

        foreach ($permissions as $perm) {
            if (str_ends_with($perm, '.*')) {
                $prefix = substr($perm, 0, -2);
                if (str_starts_with($permission, $prefix))
                    return true;
            }
        }

        return false;
    }
}

if (!function_exists('is_role')) {
    function is_role(string ...$roles): bool
    {
        $user = auth();
        if (!$user)
            return false;
        return in_array($user['role'], $roles);
    }
}
if (!function_exists('trans')) {
    function trans(string $key, array $placeholders = []): string
    {
        return \App\Core\Lang::get($key, $placeholders);
    }
}
