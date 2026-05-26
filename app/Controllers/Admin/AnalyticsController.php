<?php
/**
 * Admin\AnalyticsController — trafik analiz panelleri (Onur portu).
 *
 * Endpoint'ler:
 *   GET  /admin/analytics                  → dashboard (özet + grafik + son trafik)
 *   GET  /admin/analytics/requests         → HTTP istek logu (filtreli)
 *   GET  /admin/analytics/events           → etkileşim olayları
 *   GET  /admin/analytics/sessions         → ziyaretçi listesi
 *   GET  /admin/analytics/sessions/{id}    → ziyaretçi detayı
 *   POST /admin/analytics/event            → frontend'den olay kaydı (JSON)
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    // ─── POST /admin/analytics/event ────────────────────────────
    public function event(): Response
    {
        $body = file_get_contents('php://input') ?: '';
        $data = json_decode($body, true);
        if (!is_array($data)) $data = $_POST;

        $type   = trim((string) ($data['type'] ?? ''));
        $path   = trim((string) ($data['path'] ?? ''));
        $target = trim((string) ($data['target'] ?? ''));
        $value  = trim((string) ($data['value'] ?? ''));

        if ($type === '') {
            return $this->json(['ok' => true, 'skipped' => true], 204);
        }

        try {
            $svc = new AnalyticsService();
            $ua  = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);
            $ip  = $svc->clientIp();
            $fp  = $svc->fingerprint($ip, $ua);
            $session = $svc->getOrCreateSession($fp, $ip, $ua, $_SERVER['HTTP_REFERER'] ?? '');

            $svc->recordEvent((int) $session['id'], $type, $path, $target, $value);
            return $this->json(['ok' => true]);
        } catch (\Throwable $e) {
            return $this->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─── GET /admin/analytics (Dashboard) ───────────────────────
    public function index(): Response
    {
        $db = Database::getInstance();
        $days = max(7, min(60, (int) $this->request->query('days', 14)));

        // Overview
        $totalSessions = (int) ($db->fetch("SELECT COUNT(*) c FROM traffic_sessions")['c'] ?? 0);
        $humanSessions = (int) ($db->fetch("SELECT COUNT(*) c FROM traffic_sessions WHERE is_bot=0")['c'] ?? 0);
        $botSessions   = $totalSessions - $humanSessions;
        $todayHits     = (int) ($db->fetch("SELECT COUNT(*) c FROM traffic_logs WHERE DATE(occurred_at)=CURDATE() AND is_bot=0")['c'] ?? 0);
        $weekHits      = (int) ($db->fetch("SELECT COUNT(*) c FROM traffic_logs WHERE occurred_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND is_bot=0")['c'] ?? 0);

        // Trend (son N gün)
        $trendRows = $db->fetchAll(
            "SELECT DATE(occurred_at) AS d, COUNT(*) AS c, COUNT(DISTINCT session_id) AS uniq
             FROM traffic_logs
             WHERE is_bot=0 AND occurred_at >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
             GROUP BY DATE(occurred_at) ORDER BY d ASC"
        );
        $trend = $this->fillEmptyDays($trendRows, $days);

        // Top sayfalar (30 gün)
        $topPages = $db->fetchAll(
            "SELECT path, COUNT(*) AS hits, COUNT(DISTINCT session_id) AS uniques
             FROM traffic_logs
             WHERE is_bot=0 AND occurred_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
             GROUP BY path ORDER BY hits DESC LIMIT 12"
        );

        // Cihaz / tarayıcı / referer
        $devices = $db->fetchAll(
            "SELECT IFNULL(device,'?') AS k, COUNT(*) AS c FROM traffic_sessions WHERE is_bot=0 GROUP BY device"
        );
        $browsers = $db->fetchAll(
            "SELECT IFNULL(browser,'?') AS k, COUNT(*) AS c FROM traffic_sessions WHERE is_bot=0 GROUP BY browser ORDER BY c DESC LIMIT 6"
        );
        $sources = $db->fetchAll(
            "SELECT IF(referrer_host = '' OR referrer_host IS NULL, 'direct', referrer_host) AS src, COUNT(*) AS c
             FROM traffic_sessions WHERE is_bot=0 GROUP BY src ORDER BY c DESC LIMIT 8"
        );

        // Canlı ziyaretçiler (son 15 dakika)
        $live = $db->fetchAll(
            "SELECT s.*,
                (SELECT path FROM traffic_logs WHERE session_id=s.id ORDER BY occurred_at DESC LIMIT 1) AS last_path
             FROM traffic_sessions s
             WHERE s.is_bot=0 AND s.last_seen_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)
             ORDER BY s.last_seen_at DESC LIMIT 20"
        );

        return $this->view('admin.analytics.index', [
            'days'    => $days,
            'summary' => [
                'total_sessions' => $totalSessions,
                'human_sessions' => $humanSessions,
                'bot_sessions'   => $botSessions,
                'today_hits'     => $todayHits,
                'week_hits'      => $weekHits,
                'live_count'     => count($live),
            ],
            'trend'    => $trend,
            'topPaths' => $topPages,
            'devices'  => $devices,
            'browsers' => $browsers,
            'sources'  => $sources,
            'live'     => $live,
        ]);
    }

    // ─── GET /admin/analytics/requests ──────────────────────────
    public function requests(): Response
    {
        $db = Database::getInstance();
        $page    = max(1, (int) $this->request->query('page', 1));
        $perPage = 50;
        $offset  = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if ($p = trim((string) $this->request->query('path', '')))    { $where[] = "l.path LIKE ?";       $params[] = "%$p%"; }
        if ($ip = trim((string) $this->request->query('ip', '')))     { $where[] = "s.ip LIKE ?";         $params[] = "%$ip%"; }
        if ($st = (int) $this->request->query('status', 0))           { $where[] = "l.status_code = ?";   $params[] = $st; }
        if ($m = trim((string) $this->request->query('method', '')))  { $where[] = "l.method = ?";        $params[] = strtoupper($m); }

        $botFilter = (string) $this->request->query('bot', 'all');
        if     ($botFilter === 'human') $where[] = "l.is_bot = 0";
        elseif ($botFilter === 'bot')   $where[] = "l.is_bot = 1";

        $from = (string) $this->request->query('from', '');
        $to   = (string) $this->request->query('to', '');
        if ($from) { $where[] = "l.occurred_at >= ?"; $params[] = $from . ' 00:00:00'; }
        if ($to)   { $where[] = "l.occurred_at <= ?"; $params[] = $to   . ' 23:59:59'; }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $totalRow = $db->fetch(
            "SELECT COUNT(*) c FROM traffic_logs l LEFT JOIN traffic_sessions s ON s.id=l.session_id $whereSql",
            $params
        );
        $total = (int) ($totalRow['c'] ?? 0);
        $totalPages = (int) ceil($total / $perPage);

        $rows = $db->fetchAll(
            "SELECT l.*, s.ip, s.device, s.browser, s.os, s.country, s.is_bot AS session_bot
             FROM traffic_logs l
             LEFT JOIN traffic_sessions s ON s.id = l.session_id
             $whereSql
             ORDER BY l.occurred_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        return $this->view('admin.analytics.requests', [
            'rows'       => $rows,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $totalPages,
            'filters'    => [
                'path' => $this->request->query('path', ''),
                'ip'   => $this->request->query('ip', ''),
                'status'=> $this->request->query('status', ''),
                'method'=> $this->request->query('method', ''),
                'bot'  => $botFilter,
                'from' => $from,
                'to'   => $to,
            ],
        ]);
    }

    // ─── GET /admin/analytics/events ────────────────────────────
    public function events(): Response
    {
        $db = Database::getInstance();
        $page    = max(1, (int) $this->request->query('page', 1));
        $perPage = 50;
        $offset  = ($page - 1) * $perPage;

        $where = []; $params = [];
        if ($t = trim((string) $this->request->query('type', '')))   { $where[] = "e.event_type = ?";  $params[] = $t; }
        if ($p = trim((string) $this->request->query('path', '')))   { $where[] = "e.path LIKE ?";    $params[] = "%$p%"; }
        if ($tg= trim((string) $this->request->query('target', ''))) { $where[] = "e.target LIKE ?";  $params[] = "%$tg%"; }
        if ($ip= trim((string) $this->request->query('ip', '')))     { $where[] = "s.ip LIKE ?";      $params[] = "%$ip%"; }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = (int) ($db->fetch(
            "SELECT COUNT(*) c FROM traffic_events e LEFT JOIN traffic_sessions s ON s.id=e.session_id $whereSql",
            $params
        )['c'] ?? 0);
        $totalPages = (int) ceil($total / $perPage);

        $rows = $db->fetchAll(
            "SELECT e.*, s.ip, s.device, s.browser, s.is_bot AS session_bot
             FROM traffic_events e
             LEFT JOIN traffic_sessions s ON s.id = e.session_id
             $whereSql
             ORDER BY e.occurred_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        $types = $db->fetchAll("SELECT event_type, COUNT(*) AS c FROM traffic_events GROUP BY event_type ORDER BY c DESC");

        return $this->view('admin.analytics.events', [
            'rows'=>$rows, 'total'=>$total, 'page'=>$page, 'totalPages'=>$totalPages, 'types'=>$types,
            'filters'=>[
                'type'=>$this->request->query('type',''),
                'path'=>$this->request->query('path',''),
                'target'=>$this->request->query('target',''),
                'ip'=>$this->request->query('ip',''),
            ],
        ]);
    }

    // ─── GET /admin/analytics/sessions ──────────────────────────
    public function sessions(): Response
    {
        $db = Database::getInstance();
        $filter = (string) $this->request->query('filter', 'human');
        $where = match ($filter) {
            'bot'   => "WHERE s.is_bot=1",
            'all'   => '',
            default => "WHERE s.is_bot=0",
        };

        $rows = $db->fetchAll(
            "SELECT s.*,
                (SELECT path FROM traffic_logs WHERE session_id=s.id ORDER BY occurred_at DESC LIMIT 1) AS last_path
             FROM traffic_sessions s
             $where
             ORDER BY s.last_seen_at DESC
             LIMIT 200"
        );

        return $this->view('admin.analytics.sessions', ['rows' => $rows, 'filter' => $filter]);
    }

    // ─── GET /admin/analytics/sessions/{id} ─────────────────────
    public function sessionDetail(string $id): Response
    {
        $db = Database::getInstance();
        $session = $db->fetch("SELECT * FROM traffic_sessions WHERE id=?", [(int) $id]);
        if (!$session) return $this->notFound('Ziyaretçi bulunamadı.');

        $logs   = $db->fetchAll("SELECT * FROM traffic_logs   WHERE session_id=? ORDER BY occurred_at ASC LIMIT 500", [(int) $id]);
        $events = $db->fetchAll("SELECT * FROM traffic_events WHERE session_id=? ORDER BY occurred_at ASC LIMIT 500", [(int) $id]);
        $pages  = $db->fetchAll("SELECT path, COUNT(*) AS c FROM traffic_logs WHERE session_id=? GROUP BY path ORDER BY c DESC LIMIT 10", [(int) $id]);

        return $this->view('admin.analytics.session_detail', [
            'session' => $session, 'logs' => $logs, 'events' => $events, 'pages' => $pages,
        ]);
    }

    // ─── Internal ──────────────────────────────────────────────
    protected function fillEmptyDays(array $rows, int $days): array
    {
        $map = [];
        foreach ($rows as $r) $map[$r['d']] = ['total' => (int) $r['c'], 'uniq' => (int) ($r['uniq'] ?? 0)];

        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $out[] = ['day' => $d, 'total' => $map[$d]['total'] ?? 0, 'uniq' => $map[$d]['uniq'] ?? 0];
        }
        return $out;
    }
}
