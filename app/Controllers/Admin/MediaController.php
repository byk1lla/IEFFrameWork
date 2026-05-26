<?php
/**
 * Admin\MediaController — galeri / medya yönetimi.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Response;
use App\Core\Storage;
use App\Models\Media;

class MediaController extends Controller
{
    protected const ALLOWED = ['jpg','jpeg','png','gif','webp','svg','pdf','doc','docx','xls','xlsx','zip'];
    protected const BUCKET  = 'media';

    public function index(): Response
    {
        $page    = max(1, (int) $this->request->query('page', 1));
        $perPage = 24;
        $items   = Media::query()
            ->whereCondition('bucket', self::BUCKET)
            ->orderBy('created_at', 'DESC')
            ->take($perPage)
            ->skip(($page - 1) * $perPage)
            ->get();
        $total   = Media::count();

        return $this->view('admin.media.index', [
            'items'   => $items,
            'page'    => $page,
            'total'   => $total,
            'perPage' => $perPage,
        ]);
    }

    public function store(): Response
    {
        $this->abortIfInvalidCsrf();

        if (!$this->request->hasFile('file')) {
            $this->flash('error', 'Dosya seçilmedi.');
            return $this->redirect('/admin/media');
        }

        try {
            $file = $this->request->file('file');
            $path = Storage::put(self::BUCKET, $file, self::ALLOWED);

            Media::create([
                'disk'          => 'public',
                'bucket'        => self::BUCKET,
                'path'          => $path,
                'original_name' => substr((string) $file['name'], 0, 255),
                'mime'          => Storage::mime($path),
                'size'          => Storage::size($path),
                'alt'           => $this->request->input('alt'),
                'uploaded_by'   => Auth::id(),
            ]);

            $this->flash('success', 'Dosya yüklendi.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Yükleme hatası: ' . $e->getMessage());
        }

        return $this->redirect('/admin/media');
    }

    public function destroy(string $id): Response
    {
        $this->abortIfInvalidCsrf();

        $m = Media::find($id);
        if ($m) {
            Storage::delete((string) $m->path);
            Media::deleteById($id);
            $this->flash('success', 'Dosya silindi.');
        }
        return $this->redirect('/admin/media');
    }
}
