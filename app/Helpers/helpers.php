<?php
/**
 * Global Helpers — view/controller/her yerden çağrılabilir.
 *
 * v2.0: Carbon bağımlılığı kaldırıldı (DateTime ile).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Config;
use App\Core\Lang;
use App\Core\Session;

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('session')) {
    function session(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) return Session::all();
        return Session::get($key, $default);
    }
}

if (!function_exists('auth')) {
    function auth(): ?array
    {
        return Session::user();
    }
}

if (!function_exists('user')) {
    function user(): ?\App\Models\User
    {
        return Auth::user();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Session::csrfToken();
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
    function old(string $key, mixed $default = ''): mixed
    {
        $old = Session::getFlash('_old_input', []);
        return $old[$key] ?? $default;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $default = null): mixed
    {
        return Session::getFlash($key, $default);
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) config('app.url', ''), '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: $url");
        exit;
    }
}

if (!function_exists('back')) {
    function back(): void
    {
        redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('dd')) {
    function dd(mixed ...$vars): never
    {
        echo '<pre style="background:#f8fafc;color:#0f172a;padding:14px 16px;border-radius:6px;border:1px solid #e2e8f0;font:13px/1.5 ui-monospace,monospace;margin:12px;">';
        foreach ($vars as $v) var_dump($v);
        echo '</pre>';
        exit;
    }
}

if (!function_exists('dump')) {
    function dump(mixed ...$vars): void
    {
        echo '<pre style="background:#f8fafc;color:#0f172a;padding:14px 16px;border-radius:6px;border:1px solid #e2e8f0;font:13px/1.5 ui-monospace,monospace;margin:12px;">';
        foreach ($vars as $v) var_dump($v);
        echo '</pre>';
    }
}

if (!function_exists('now')) {
    function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone((string) config('app.timezone', 'Europe/Istanbul')));
    }
}

if (!function_exists('uuid')) {
    function uuid(): string
    {
        return \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
    }
}

if (!function_exists('format_money')) {
    function format_money(mixed $amount, string $currency = '₺'): string
    {
        return number_format((float) $amount, 2, ',', '.') . ' ' . $currency;
    }
}

if (!function_exists('format_date')) {
    function format_date(mixed $date, string $format = 'd.m.Y'): string
    {
        if ($date === null || $date === '') return '-';
        $dt = $date instanceof \DateTimeInterface ? $date : new \DateTimeImmutable((string) $date);
        return $dt->format($format);
    }
}

if (!function_exists('format_datetime')) {
    function format_datetime(mixed $date, string $format = 'd.m.Y H:i'): string
    {
        return format_date($date, $format);
    }
}

if (!function_exists('format_phone')) {
    function format_phone(?string $phone): string
    {
        if (!$phone) return '-';
        $c = preg_replace('/[^0-9]/', '', $phone) ?? '';
        if (strlen($c) === 10) {
            return sprintf('(%s) %s %s %s', substr($c,0,3), substr($c,3,3), substr($c,6,2), substr($c,8,2));
        }
        return $phone;
    }
}

if (!function_exists('str_limit')) {
    function str_limit(?string $value, int $limit = 100, string $end = '...'): string
    {
        if (!$value) return '';
        return mb_strlen($value) <= $limit ? $value : mb_substr($value, 0, $limit) . $end;
    }
}

if (!function_exists('is_current')) {
    function is_current(string $path): bool
    {
        $cur = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        return $cur === $path || str_starts_with($cur, rtrim($path, '/') . '/');
    }
}

if (!function_exists('class_active')) {
    function class_active(string $path, string $class = 'active'): string
    {
        return is_current($path) ? $class : '';
    }
}

if (!function_exists('can')) {
    /**
     * RBAC kontrol — config/permissions.php matrisinden bakar.
     * Örn: can('blog.delete'), can('admin.users.edit')
     */
    function can(string $permission): bool
    {
        $u = auth();
        if (!$u) return false;
        $role = $u['role'] ?? 'user';
        if ($role === 'superadmin') return true;

        // Önce config/permissions.php → roles map
        $perms = (array) config("permissions.roles.{$role}.permissions", []);
        // Geriye dönük: config/app.php → roles map (eski sürüm)
        if (!$perms) {
            $perms = (array) config("app.roles.{$role}.permissions", []);
        }

        if (in_array('*', $perms, true)) return true;
        if (in_array($permission, $perms, true)) return true;
        foreach ($perms as $p) {
            if (str_ends_with($p, '.*') && str_starts_with($permission, substr($p, 0, -2))) return true;
        }
        return false;
    }
}

if (!function_exists('role_label')) {
    function role_label(?string $role): string
    {
        if (!$role) return '—';
        return (string) (config("permissions.roles.{$role}.label") ?? config("app.roles.{$role}.name") ?? $role);
    }
}

if (!function_exists('is_role')) {
    function is_role(string ...$roles): bool
    {
        $u = auth();
        return $u !== null && in_array($u['role'] ?? null, $roles, true);
    }
}

if (!function_exists('trans')) {
    function trans(string $key, array $placeholders = []): string
    {
        return Lang::get($key, $placeholders);
    }
}

if (!function_exists('__')) {
    function __(string $key, array $placeholders = []): string
    {
        return Lang::get($key, $placeholders);
    }
}

if (!function_exists('is_editing')) {
    /**
     * Inline editör modu aktif mi? ?edit=1 + admin/editor role.
     */
    function is_editing(): bool
    {
        if (($_GET['edit'] ?? '') !== '1') return false;
        $u = auth();
        if (!$u) return false;
        return in_array($u['role'] ?? 'user', ['superadmin', 'admin', 'editor'], true);
    }
}

if (!function_exists('editable')) {
    /**
     * Inline edit destekli içerik bloğu.
     *
     * Kullanım:
     *   {!! editable('home.hero.title', 'Hoş geldin!', ['tag'=>'h1', 'class'=>'text-4xl']) !!}
     *   {!! editable('home.about.body', '<p>...</p>', ['type'=>'html', 'tag'=>'div']) !!}
     *
     * Edit modunda: data-editable attribute + dashed border ile düzenlenebilir.
     * Normal modda: düz HTML render.
     *
     * @param array{tag?:string,class?:string,type?:string} $opts
     */
    function editable(string $pageDotKey, ?string $default = null, array $opts = []): string
    {
        // SiteContent (Onur) ile birleştirildi — aynı key, aynı store
        $val   = \App\Core\SiteContent::get($pageDotKey, (string) ($default ?? ''));
        $type  = (string) ($opts['type']  ?? 'text');
        $tag   = (string) ($opts['tag']   ?? 'span');
        $class = (string) ($opts['class'] ?? '');

        $safe  = $type === 'html' ? $val : e($val);

        if (\App\Core\SiteContent::isEditing()) {
            $cls = trim('osk-editable ' . $class);
            return sprintf(
                '<%s data-editable="%s" data-editable-type="%s" data-editable-key="%s" class="%s">%s</%s>',
                $tag,
                e($pageDotKey),
                e($type),
                e($pageDotKey),
                e($cls),
                $safe,
                $tag
            );
        }

        $clsAttr = $class !== '' ? ' class="' . e($class) . '"' : '';
        return "<{$tag}{$clsAttr}>{$safe}</{$tag}>";
    }
}

if (!function_exists('editable_icon')) {
    /**
     * Inline edit destekli Font Awesome icon.
     *   {!! editable_icon('home.feature.icon1', 'fa-solid fa-bolt', ['class'=>'text-2xl']) !!}
     */
    function editable_icon(string $pageDotKey, string $default = 'fa-solid fa-star', array $opts = []): string
    {
        $cls   = \App\Core\SiteContent::get($pageDotKey, $default) ?: $default;
        $extra = e((string) ($opts['class'] ?? ''));

        if (\App\Core\SiteContent::isEditing()) {
            return sprintf(
                '<span class="osk-editable-icon %s" data-editable="%s" data-editable-type="icon" data-editable-key="%s"><i class="%s"></i></span>',
                $extra,
                e($pageDotKey),
                e($pageDotKey),
                e($cls)
            );
        }
        return sprintf('<i class="%s %s"></i>', e($cls), $extra);
    }
}

if (!function_exists('editable_image')) {
    /**
     * Inline edit destekli görsel.
     *   {!! editable_image('home.hero.cover', '/assets/img/default.jpg', ['alt'=>'Hero','class'=>'w-full']) !!}
     */
    function editable_image(string $pageDotKey, string $default = '', array $opts = []): string
    {
        $src   = \App\Core\SiteContent::get($pageDotKey, $default) ?: $default;
        $alt   = e((string) ($opts['alt']   ?? ''));
        $class = e((string) ($opts['class'] ?? ''));

        $img = sprintf('<img src="%s" alt="%s" class="%s">', e($src), $alt, $class);

        if (\App\Core\SiteContent::isEditing()) {
            return sprintf(
                '<div class="osk-editable-image" data-editable="%s" data-editable-type="image" data-editable-key="%s">%s</div>',
                e($pageDotKey),
                e($pageDotKey),
                $img
            );
        }
        return $img;
    }
}

// ─── Onur tarzı canlı editör helper'ları (SiteContent KV) ─────────────────
if (!function_exists('content')) {
    /**
     * Site içeriği için canlı editör destekli helper.
     */
    function content(string $key, string $default = '', array $opts = []): string
    {
        $val   = \App\Core\SiteContent::get($key, $default);
        $tag   = $opts['tag']   ?? 'span';
        $class = $opts['class'] ?? '';
        $type  = $opts['type']  ?? 'text';

        if (\App\Core\SiteContent::isEditing()) {
            $attrs = sprintf(
                ' data-editable="%s" data-editable-type="%s" data-editable-key="%s" class="%s"',
                htmlspecialchars($key, ENT_QUOTES),
                htmlspecialchars($type, ENT_QUOTES),
                htmlspecialchars($key, ENT_QUOTES),
                htmlspecialchars(trim($class . ' osk-editable'), ENT_QUOTES)
            );
            $safeVal = $type === 'html' ? $val : htmlspecialchars($val, ENT_QUOTES);
            return "<{$tag}{$attrs}>{$safeVal}</{$tag}>";
        }

        $safeVal = $type === 'html' ? $val : htmlspecialchars($val, ENT_QUOTES);
        $classAttr = $class !== '' ? ' class="' . htmlspecialchars($class, ENT_QUOTES) . '"' : '';
        return "<{$tag}{$classAttr}>{$safeVal}</{$tag}>";
    }
}

if (!function_exists('content_text')) {
    function content_text(string $key, string $default = ''): string
    {
        return \App\Core\SiteContent::get($key, $default);
    }
}

if (!function_exists('content_image')) {
    function content_image(string $key, string $default = '', array $opts = []): string
    {
        $src = \App\Core\SiteContent::get($key, $default);
        $alt = htmlspecialchars($opts['alt'] ?? '', ENT_QUOTES);
        $class = htmlspecialchars($opts['class'] ?? '', ENT_QUOTES);
        $wrapperClass = htmlspecialchars($opts['wrapper_class'] ?? '', ENT_QUOTES);

        $img = sprintf('<img src="%s" alt="%s" class="%s">', htmlspecialchars($src, ENT_QUOTES), $alt, $class);

        if (\App\Core\SiteContent::isEditing()) {
            return sprintf(
                '<div class="osk-editable-image %s" data-editable="%s" data-editable-type="image" data-editable-key="%s">%s</div>',
                $wrapperClass,
                htmlspecialchars($key, ENT_QUOTES),
                htmlspecialchars($key, ENT_QUOTES),
                $img
            );
        }
        return $wrapperClass !== '' ? '<div class="' . $wrapperClass . '">' . $img . '</div>' : $img;
    }
}

if (!function_exists('content_icon')) {
    function content_icon(string $key, string $default = 'fa-solid fa-star', array $opts = []): string
    {
        $cls = \App\Core\SiteContent::get($key, $default);
        $extra = htmlspecialchars($opts['class'] ?? '', ENT_QUOTES);

        if (\App\Core\SiteContent::isEditing()) {
            return sprintf(
                '<span class="osk-editable-icon %s" data-editable="%s" data-editable-type="icon" data-editable-key="%s"><i class="%s"></i></span>',
                $extra,
                htmlspecialchars($key, ENT_QUOTES),
                htmlspecialchars($key, ENT_QUOTES),
                htmlspecialchars($cls, ENT_QUOTES)
            );
        }
        return sprintf('<i class="%s %s"></i>', htmlspecialchars($cls, ENT_QUOTES), $extra);
    }
}
