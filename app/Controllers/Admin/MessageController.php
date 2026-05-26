<?php
/**
 * Admin\MessageController — iletişim mesajları yönetimi.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Response;
use App\Models\Message;

class MessageController extends Controller
{
    public function index(): Response
    {
        $page    = max(1, (int) $this->request->query('page', 1));
        $perPage = 20;
        $filter  = (string) $this->request->query('filter', 'all'); // all | unread

        $q = Message::query();
        if ($filter === 'unread') $q->whereCondition('is_read', 0);
        $messages = $q->orderBy('created_at', 'DESC')->take($perPage)->skip(($page - 1) * $perPage)->get();

        $unreadCount = Message::query()->whereCondition('is_read', 0)->get();
        $total       = Message::count();

        return $this->view('admin.messages.index', [
            'messages'    => $messages,
            'page'        => $page,
            'perPage'     => $perPage,
            'filter'      => $filter,
            'unreadCount' => count($unreadCount),
            'total'       => $total,
        ]);
    }

    public function show(string $id): Response
    {
        $message = Message::find($id);
        if (!$message) return $this->notFound('Mesaj bulunamadı.');

        if (!$message->is_read) $message->markRead();

        return $this->view('admin.messages.show', ['m' => $message]);
    }

    public function destroy(string $id): Response
    {
        $this->abortIfInvalidCsrf();
        Message::deleteById($id);
        $this->flash('success', 'Mesaj silindi.');
        return $this->redirect('/admin/messages');
    }
}
