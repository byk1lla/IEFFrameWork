<?php
/**
 * Router Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

class Router
{
    protected static array $routes = [];
    protected static array $groupStack = [];
    protected static array $patterns = [
        '{id}' => '([a-zA-Z0-9\-]+)',
        '{uuid}' => '([a-zA-Z0-9\-]+)',
        '{orderId}' => '([a-zA-Z0-9\-]+)',
        '{routeId}' => '([a-zA-Z0-9\-]+)',
        '{stopId}' => '([a-zA-Z0-9\-]+)',
    ];

    public static function get(string $uri, $action): void
    {
        self::addRoute('GET', $uri, $action);
    }

    public static function post(string $uri, $action): void
    {
        self::addRoute('POST', $uri, $action);
    }

    public static function put(string $uri, $action): void
    {
        self::addRoute('PUT', $uri, $action);
    }

    public static function delete(string $uri, $action): void
    {
        self::addRoute('DELETE', $uri, $action);
    }

    public static function group(array $attributes, callable $callback): void
    {
        self::$groupStack[] = $attributes;
        $callback();
        array_pop(self::$groupStack);
    }

    protected static function addRoute(string $method, string $uri, $action): void
    {
        $prefix = '';
        $middleware = [];

        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix .= $group['prefix'];
            }
            if (isset($group['middleware'])) {
                $middleware = array_merge($middleware, (array) $group['middleware']);
            }
        }

        $uri = $prefix . $uri;
        $uri = $uri === '' ? '/' : $uri;

        self::$routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public static function fallback(callable $callback): void
    {
        self::$routes['fallback'] = $callback;
    }

    public static function getRoutes(): array
    {
        return self::$routes;
    }

    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        // PUT/DELETE için method override
        if ($method === 'POST') {
            // Form _method field
            if (isset($_POST['_method'])) {
                $method = strtoupper($_POST['_method']);
            }
            // JSON body _method
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['_method'])) {
                $method = strtoupper($input['_method']);
            }
            // X-HTTP-Method-Override header
            if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
                $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
            }
        }

        // Debug log
        error_log("Router dispatch: method=$method, uri=$uri");

        foreach (self::$routes as $key => $route) {
            if ($key === 'fallback')
                continue;

            if ($route['method'] !== $method)
                continue;

            $pattern = self::convertToPattern($route['uri']);

            if (preg_match($pattern, $uri, $matches)) {
                error_log("Route matched: " . $route['uri'] . " -> " . (is_string($route['action']) ? $route['action'] : 'closure'));
                array_shift($matches);

                // Middleware çalıştır
                foreach ($route['middleware'] as $middleware) {
                    if (!self::runMiddleware($middleware)) {
                        return;
                    }
                }

                // Action çalıştır
                self::runAction($route['action'], $matches);
                return;
            }
        }

        // 404
        if (isset(self::$routes['fallback'])) {
            (self::$routes['fallback'])();
        } else {
            http_response_code(404);
            echo '404 Not Found';
        }
    }

    protected static function convertToPattern(string $uri): string
    {
        $pattern = preg_quote($uri, '#');

        foreach (self::$patterns as $placeholder => $regex) {
            $pattern = str_replace(preg_quote($placeholder, '#'), $regex, $pattern);
        }

        return '#^' . $pattern . '$#';
    }

    protected static function runMiddleware(string $middleware): bool
    {
        $parts = explode(':', $middleware);
        $name = $parts[0];
        $params = isset($parts[1]) ? explode(',', $parts[1]) : [];

        $class = 'App\\Middleware\\' . ucfirst($name) . 'Middleware';

        if (class_exists($class)) {
            $instance = new $class();
            return $instance->handle(Request::getInstance(), $params);
        }

        return true;
    }

    protected static function runAction($action, array $params = []): void
    {
        if (is_callable($action)) {
            call_user_func_array($action, $params);
            return;
        }

        if (is_string($action)) {
            [$controller, $method] = explode('@', $action);
            $class = 'App\\Controllers\\' . $controller;

            if (class_exists($class)) {
                $instance = new $class();

                // Reflection ile method parametrelerini kontrol et
                $reflection = new \ReflectionMethod($instance, $method);
                $methodParams = [];
                $paramIndex = 0;

                foreach ($reflection->getParameters() as $param) {
                    $type = $param->getType();

                    // Request tipinde parametre varsa enjekte et
                    if ($type && !$type->isBuiltin()) {
                        $typeName = $type->getName();
                        if ($typeName === 'App\\Core\\Request' || $typeName === Request::class) {
                            $methodParams[] = Request::getInstance();
                            continue;
                        }
                    }

                    // Route parametrelerinden al
                    if (isset($params[$paramIndex])) {
                        $methodParams[] = $params[$paramIndex];
                        $paramIndex++;
                    } elseif ($param->isDefaultValueAvailable()) {
                        $methodParams[] = $param->getDefaultValue();
                    }
                }

                call_user_func_array([$instance, $method], $methodParams);
            }
        }
    }
}
