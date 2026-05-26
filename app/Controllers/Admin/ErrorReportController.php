<?php
/**
 * Admin\ErrorReportController — exception sayfasından gelen hata raporları.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;

class ErrorReportController extends Controller
{
    public function index(): Response
    {
        $rows = Database::getInstance()->fetchAll(
            "SELECT id, hash, exception, message, file, line, occurrence_count, mail_sent_count,
                    is_fixed, first_seen_at, last_seen_at, fixed_at
               FROM error_reports
              ORDER BY is_fixed ASC, last_seen_at DESC"
        );

        // İstatistikler
        $stats = [
            'total'    => count($rows),
            'open'     => count(array_filter($rows, fn ($r) => !$r['is_fixed'])),
            'fixed'    => count(array_filter($rows, fn ($r) => $r['is_fixed'])),
            'today'    => count(array_filter($rows, fn ($r) => str_starts_with((string) $r['last_seen_at'], date('Y-m-d')))),
            'mostHit'  => $rows[0] ?? null,
        ];

        return $this->view('admin.errors.index', [
            'rows'  => $rows,
            'stats' => $stats,
        ]);
    }

    public function show(int $id): Response
    {
        $row = Database::getInstance()->fetch("SELECT * FROM error_reports WHERE id = ?", [$id]);
        if (!$row) {
            $this->flash('error', 'Hata kaydı bulunamadı.');
            return $this->redirect('/admin/error-reports');
        }

        // JSON alanlarını decode et
        $row['trace_decoded']    = $row['trace']        ? (json_decode($row['trace'],        true) ?: $row['trace']) : null;
        $row['request_decoded']  = $row['request_data'] ? (json_decode($row['request_data'], true) ?: $row['request_data']) : null;
        $row['server_decoded']   = $row['server_data']  ? (json_decode($row['server_data'],  true) ?: $row['server_data'])  : null;

        return $this->view('admin.errors.show', ['row' => $row]);
    }

    public function fix(int $id): Response
    {
        $this->abortIfInvalidCsrf();
        \App\Models\ErrorReport::markFixed($id);
        $this->flash('success', 'Hata "fixed" olarak işaretlendi.');
        return $this->redirect('/admin/error-reports/' . $id);
    }

    public function destroy(int $id): Response
    {
        $this->abortIfInvalidCsrf();
        Database::getInstance()->execute("DELETE FROM error_reports WHERE id = ?", [$id]);
        $this->flash('success', 'Hata kaydı silindi.');
        return $this->redirect('/admin/error-reports');
    }

    public function clear(): Response
    {
        $this->abortIfInvalidCsrf();
        $deleted = Database::getInstance()->execute("DELETE FROM error_reports WHERE is_fixed = 1");
        $this->flash('success', $deleted . ' adet kapatılmış hata silindi.');
        return $this->redirect('/admin/error-reports');
    }
}
