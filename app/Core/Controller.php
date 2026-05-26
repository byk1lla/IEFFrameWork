<?php
/**
 * Controller — tüm controller'ların base sınıfı.
 *
 * Sağladığı yardımcılar: view(), json(), redirect(), back(), validateCsrf(),
 * flash(), authorize().
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected Request  $request;
    protected Response $response;
    protected array    $data = [];

    public function __construct()
    {
        $this->request  = Request::getInstance();
        $this->response = new Response();
    }

    /**
     * View render et + auth user ve csrf'i otomatik geçir.
     */
    protected function view(string $view, array $data = []): Response
    {
        $merged = array_merge($this->data, [
            'authUser'   => Auth::user()?->toArray(),
            'csrf_token' => Session::csrfToken(),
        ], $data);

        $this->response->setContent(View::render($view, $merged));
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

    protected function jsonOk(string $message = 'OK', array $data = []): Response
    {
        return $this->json(['success' => true, 'message' => $message] + $data);
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

    protected function validateCsrf(): bool
    {
        $token = $this->request->input('_csrf_token') ?? $this->request->header('X-CSRF-TOKEN');
        return Session::verifyCsrfToken(is_string($token) ? $token : null);
    }

    protected function abortIfInvalidCsrf(): void
    {
        if (!$this->validateCsrf()) {
            Response::abort(419);
        }
    }

    protected function flash(string $type, string $message): void
    {
        Session::flash($type, $message);
    }

    protected function authorize(string|array $roleOrRoles): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        $role = $user->role ?? null;
        return is_array($roleOrRoles) ? in_array($role, $roleOrRoles, true) : $role === $roleOrRoles;
    }

    protected function notFound(string $message = 'Sayfa bulunamadı'): Response
    {
        http_response_code(404);
        return $this->view('errors.404', ['message' => $message]);
    }
}
