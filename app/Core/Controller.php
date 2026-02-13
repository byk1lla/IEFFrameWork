<?php

namespace App\Core;

/**
 * Base Controller Class
 */
abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected ?array $user = null;
    protected array $data = [];

    public function __construct()
    {
        $this->request = Request::getInstance();
        $this->response = new Response();
        $this->user = Session::get('user');
    }

    protected function view(string $view, array $data = []): Response
    {
        $data['authUser'] = $this->user;
        $data['csrf_token'] = Session::getCsrfToken();

        $content = View::render($view, array_merge($this->data, $data));

        $this->response->setContent($content);
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
        $this->response->back();
        return $this->response;
    }

    protected function authorize(string|array $permission): bool
    {
        if (!$this->user)
            return false;

        $role = $this->user['role'] ?? 'user';

        if (is_array($permission)) {
            return in_array($role, $permission);
        }

        // Basic permission check (can be expanded)
        return $role === 'admin';
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

    protected function notFound(string $message = 'Sayfa bulunamadı'): Response
    {
        http_response_code(404);
        return $this->view('errors/404', ['message' => $message]);
    }
}
