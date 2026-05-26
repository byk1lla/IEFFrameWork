<?php
/**
 * Admin\UserController — kullanıcı yönetimi (CRUD + rol).
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Validator;
use App\Models\User;

class UserController extends Controller
{
    /** Sadece bu role'ler kullanıcı yönetimine erişebilir */
    protected const ALLOWED_ROLES = ['admin', 'superadmin'];

    public function index(): Response
    {
        $this->ensureAdmin();
        $users = User::query()->orderBy('id', 'DESC')->take(100)->get();
        return $this->view('admin.users.index', ['users' => $users]);
    }

    public function create(): Response
    {
        $this->ensureAdmin();
        return $this->view('admin.users.edit', [
            'user' => new User(),
            'mode' => 'create',
        ]);
    }

    public function store(): Response
    {
        $this->ensureAdmin();
        $this->abortIfInvalidCsrf();

        $v = Validator::make($this->request->all(), [
            'name'     => 'required|min:2|max:120',
            'email'    => 'required|email|max:191|unique:users,email',
            'password' => 'required|min:6|max:191',
            'role'     => 'required|in:user,admin,superadmin',
        ]);
        if ($v->fails()) {
            $errs  = $v->errors();
            $first = reset($errs);
            $this->flash('error', is_array($first) ? ($first[0] ?? 'Form hatası') : (string) $first);
            return $this->redirect('/admin/users/create');
        }

        User::register([
            'name'  => trim((string) $this->request->input('name')),
            'email' => strtolower(trim((string) $this->request->input('email'))),
            'password' => (string) $this->request->input('password'),
            'role'  => (string) $this->request->input('role', 'user'),
        ]);

        $this->flash('success', 'Kullanıcı oluşturuldu.');
        return $this->redirect('/admin/users');
    }

    public function edit(string $id): Response
    {
        $this->ensureAdmin();
        $user = User::find($id);
        if (!$user) return $this->notFound();
        return $this->view('admin.users.edit', ['user' => $user, 'mode' => 'edit']);
    }

    public function update(string $id): Response
    {
        $this->ensureAdmin();
        $this->abortIfInvalidCsrf();

        $user = User::find($id);
        if (!$user) return $this->notFound();

        $v = Validator::make($this->request->all(), [
            'name'  => 'required|min:2|max:120',
            'email' => 'required|email|max:191|unique:users,email,' . $id,
            'role'  => 'required|in:user,admin,superadmin',
        ]);
        if ($v->fails()) {
            $errs  = $v->errors();
            $first = reset($errs);
            $this->flash('error', is_array($first) ? ($first[0] ?? 'Form hatası') : (string) $first);
            return $this->redirect("/admin/users/$id/edit");
        }

        $data = [
            'name'  => trim((string) $this->request->input('name')),
            'email' => strtolower(trim((string) $this->request->input('email'))),
            'role'  => (string) $this->request->input('role'),
        ];
        $newPassword = (string) $this->request->input('password', '');
        if ($newPassword !== '') {
            if (mb_strlen($newPassword) < 6) {
                $this->flash('error', 'Şifre en az 6 karakter olmalıdır.');
                return $this->redirect("/admin/users/$id/edit");
            }
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        User::updateById($id, $data);
        $this->flash('success', 'Kullanıcı güncellendi.');
        return $this->redirect("/admin/users/$id/edit");
    }

    public function destroy(string $id): Response
    {
        $this->ensureAdmin();
        $this->abortIfInvalidCsrf();

        if ((string) Auth::id() === (string) $id) {
            $this->flash('error', 'Kendi hesabını silemezsin.');
            return $this->redirect('/admin/users');
        }

        User::deleteById($id);
        $this->flash('success', 'Kullanıcı silindi.');
        return $this->redirect('/admin/users');
    }

    // ─── Internal ──────────────────────────────────────────────────
    protected function ensureAdmin(): void
    {
        $u = Auth::user();
        if (!$u || !in_array($u->role ?? 'user', self::ALLOWED_ROLES, true)) {
            \App\Core\Response::abort(403);
        }
    }
}
