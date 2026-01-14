<?php
/**
 * Temel Controller Sınıfı
 * 
 * @package    IEF Framework
 */

namespace App\Core;

abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected ?array $user = null;

    public function __construct()
    {
        $this->request = Request::getInstance();
        $this->response = new Response();
        $this->user = Session::get('user');
    }

    protected function view(string $view, array $data = []): Response
    {
        $data['authUser'] = $this->user; // Oturum açmış kullanıcı (layout için)
        $data['csrf_token'] = Session::getCsrfToken();
        
        extract($data);
        
        $viewPath = VIEW_PATH . '/' . str_replace('.', '/', $view) . '.php';
        
        if (file_exists($viewPath)) {
            ob_start();
            include $viewPath;
            $content = ob_get_clean();
            
            // Layout wrap
            if (empty($layout) && isset($data['layout'])) {
                $layout = $data['layout'];
            }
            
            if (!empty($layout)) {
                $layoutPath = VIEW_PATH . '/layouts/' . $layout . '.php';
                if (file_exists($layoutPath)) {
                    ob_start();
                    include $layoutPath;
                    $content = ob_get_clean();
                }
            }
            
            echo $content;
        } else {
            throw new \Exception("View not found: {$view}");
        }
        
        return $this->response;
    }

    protected function json(array $data, int $status = 200): Response
    {
        $this->response->json($data, $status);
        return $this->response;
    }

    protected function jsonError(string $message, int $status = 400): Response
    {
        return $this->json(['success' => false, 'message' => $message], $status);
    }

    protected function redirect(string $url, int $status = 302): Response
    {
        $this->response->redirect($url, $status);
        return $this->response;
    }

    protected function back(): Response
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
        return $this->response;
    }

    /**
     * Yetkilendirme kontrolü
     * @param string|array $permission String ise permission, array ise roller
     */
    protected function authorize(string|array $permission): bool
    {
        if (!$this->user) return false;

        $role = $this->user['role'];
        
        // Eğer array ise rol kontrolü yap
        if (is_array($permission)) {
            return in_array($role, $permission);
        }
        
        // String ise permission kontrolü yap
        $appConfig = require CONFIG_PATH . '/app.php';
        $roleConfig = $appConfig['roles'][$role] ?? null;

        if (!$roleConfig) return false;

        if (in_array('*', $roleConfig['permissions'])) return true;

        foreach ($roleConfig['permissions'] as $perm) {
            if ($perm === $permission) return true;
            if (str_ends_with($perm, '.*')) {
                $prefix = substr($perm, 0, -2);
                if (str_starts_with($permission, $prefix)) return true;
            }
        }

        return false;
    }

    protected function validateCsrf(): bool
    {
        $token = $this->request->input('_csrf_token') ?? $this->request->header('X-CSRF-TOKEN');
        return Session::verifyCsrfToken($token);
    }

    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    /**
     * 404 Not Found sayfası göster
     */
    protected function notFound(string $message = 'Sayfa bulunamadı'): Response
    {
        http_response_code(404);
        return $this->view('errors/404', ['message' => $message]);
    }
}
