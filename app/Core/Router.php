<?php
/**
 * Router — basit, hızlı route engine.
 *
 * Özellikler:
 *   - GET/POST/PUT/DELETE/PATCH + method override (form _method, X-HTTP-Method-Override)
 *   - Group + prefix + middleware miras
 *   - Named parameters: {id}, {slug}, {locale}
 *   - Method injection (Request, route params)
 *   - resource() helper (RESTful)
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class Router
{
    /** @var array<int|string, array<string,mixed>> */
    protected static array $routes     = [];
    /** @var array<int, array<string,mixed>> */
    protected static array $groupStack = [];

    /** @var array<string,string> */
    protected static array $patterns = [
        '{id}'     => '([a-zA-Z0-9\-]+)',
        '{uuid}'   => '([a-zA-Z0-9\-]+)',
        '{slug}'   => '([a-zA-Z0-9\-_]+)',
        '{locale}' => '([a-zA-Z]{2})',
    ];

    // ─── HTTP method'lar ─────────────────────────────────────────────
    public static function get(string $uri, mixed $action, array $options = []): void    { self::add('GET',    $uri, $action, $options); }
    public static function post(string $uri, mixed $action, array $options = []): void   { self::add('POST',   $uri, $action, $options); }
    public static function put(string $uri, mixed $action, array $options = []): void    { self::add('PUT',    $uri, $action, $options); }
    public static function patch(string $uri, mixed $action, array $options = []): void  { self::add('PATCH',  $uri, $action, $options); }
    public static function delete(string $uri, mixed $action, array $options = []): void { self::add('DELETE', $uri, $action, $options); }

    public static function any(string $uri, mixed $action, array $options = []): void
    {
        foreach (['GET','POST','PUT','PATCH','DELETE'] as $m) self::add($m, $uri, $action, $options);
    }

    public static function fallback(callable $cb): void
    {
        self::$routes['fallback'] = ['callback' => $cb];
    }

    public static function group(array $attributes, callable $callback): void
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    /**
     * RESTful resource: index/create/store/show/edit/update/destroy
     */
    public static function resource(string $uri, string $controller): void
    {
        self::get   ($uri,            "$controller@index");
        self::get   ("$uri/create",   "$controller@create");
        self::post  ($uri,            "$controller@store");
        self::get   ("$uri/{id}",     "$controller@show");
        self::get   ("$uri/{id}/edit","$controller@edit");
        self::put   ("$uri/{id}",     "$controller@update");
        self::patch ("$uri/{id}",     "$controller@update");
        self::delete("$uri/{id}",     "$controller@destroy");
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    // ─── Dispatch ───────────────────────────────────────────────────
    public static function dispatch(): void
    {
        $request = Request::getInstance();
        $method  = $request->method();
        $uri     = $request->uri();
        $uri     = rtrim($uri, '/') ?: '/';

        foreach (self::$routes as $key => $route) {
            if ($key === 'fallback') continue;
            if ($route['method'] !== $method) continue;

            $pattern = self::compilePattern($route['uri']);
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                DebugBar::getInstance()->setRoute("{$route['method']} {$route['uri']}");

                // Middleware'leri çalıştır
                foreach ($route['middleware'] as $mw) {
                    if (!self::runMiddleware($mw, $request)) return;
                }

                self::runAction($route['action'], $matches);
                return;
            }
        }

        // 404
        if (isset(self::$routes['fallback'])) {
            (self::$routes['fallback']['callback'])();
            return;
        }
        http_response_code(404);
        echo '404 Not Found';
    }

    // ─── Internal ───────────────────────────────────────────────────
    protected static function add(string $method, string $uri, mixed $action, array $options = []): void
    {
        $prefix     = '';
        $middleware = (array) ($options['middleware'] ?? []);

        foreach (self::$groupStack as $group) {
            if (isset($group['prefix']))     $prefix     .= $group['prefix'];
            if (isset($group['middleware'])) $middleware  = array_merge($middleware, (array) $group['middleware']);
        }

        $full = $prefix . $uri;
        $full = rtrim($full, '/');
        if ($full === '') $full = '/';

        self::$routes[] = [
            'method'     => $method,
            'uri'        => $full,
            'action'     => $action,
            'middleware' => $middleware,
        ];
    }

    protected static function compilePattern(string $uri): string
    {
        $pattern = preg_quote($uri, '#');

        // Önce özel adlandırılmış pattern'lar
        foreach (self::$patterns as $ph => $rx) {
            $pattern = str_replace(preg_quote($ph, '#'), $rx, $pattern);
        }
        // Generic {name} → ([^/]+)
        $pattern = preg_replace('#\\\\\{[a-zA-Z0-9_]+\\\\\}#', '([^/]+)', $pattern);

        return '#^' . $pattern . '$#';
    }

    protected static function runMiddleware(string $mw, Request $request): bool
    {
        $parts  = explode(':', $mw, 2);
        $name   = $parts[0];
        $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

        // Tam class adı verilmişse onu kullan; yoksa Middleware\<Name>Middleware
        if (class_exists($name)) {
            $class = $name;
        } else {
            $class = 'App\\Middleware\\' . ucfirst($name) . 'Middleware';
            if (!class_exists($class)) return true;
        }

        return (new $class())->handle($request, $params);
    }

    protected static function runAction(mixed $action, array $params = []): void
    {
        $result = null;

        if (is_callable($action)) {
            $result = call_user_func_array($action, $params);
        } elseif (is_string($action) && str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action);
            // Tam namespace verilmişse (App\... ile başlıyor) doğrudan kullan; aksi halde
            // App\Controllers\ prefix'i ekle (alt klasörler için "Admin\MessageController" gibi).
            $class = str_starts_with($controller, 'App\\')
                ? $controller
                : 'App\\Controllers\\' . $controller;
            if (!class_exists($class)) {
                http_response_code(500);
                echo "Controller bulunamadı: $class";
                return;
            }
            $instance = new $class();
            $args     = self::resolveMethodArgs($instance, $method, $params);
            $result   = $instance->$method(...$args);
        }

        if ($result instanceof Response) {
            $result->send();
        } elseif (is_string($result)) {
            echo $result;
        } elseif (is_array($result)) {
            (new Response())->json($result);
        }
    }

    protected static function resolveMethodArgs(object $instance, string $method, array $routeParams): array
    {
        $ref     = new \ReflectionMethod($instance, $method);
        $args    = [];
        $rpIdx   = 0;

        foreach ($ref->getParameters() as $param) {
            $type = $param->getType();
            if ($type instanceof \ReflectionNamedType && !$type->isBuiltin()) {
                $tn = $type->getName();
                if ($tn === Request::class) {
                    $args[] = Request::getInstance();
                    continue;
                }
            }
            if (isset($routeParams[$rpIdx])) {
                $args[] = $routeParams[$rpIdx++];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                $args[] = null;
            }
        }
        return $args;
    }
}
