<?php
/**
 * Debug Bar — gerçek zamanlı geliştirici çubuğu (SQL, latency, memory).
 *
 * Sadece debug=true ve HTML response'larda enjekte edilir.
 *
 * @package IEF Framework
 */

declare(strict_types=1);

namespace App\Core;

class DebugBar
{
    protected static ?self $instance = null;

    protected array  $queries     = [];
    protected float  $startTime;
    protected string $currentRoute = '—';

    private function __construct()
    {
        $this->startTime = microtime(true);
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function setRoute(string $route): void { $this->currentRoute = $route; }

    public function logQuery(string $sql, array $params, float $time): void
    {
        $this->queries[] = [
            'sql'    => $sql,
            'params' => $params,
            'time'   => round($time * 1000, 2),
        ];
    }

    public function getQueries(): array       { return $this->queries; }
    public function getExecutionMs(): float   { return round((microtime(true) - $this->startTime) * 1000, 2); }
    public function getMemoryMb(): float      { return round(memory_get_peak_usage() / 1024 / 1024, 2); }

    public function render(): string
    {
        if (!ExceptionHandler::isDebug()) return '';

        $queries  = $this->queries;
        $count    = count($queries);
        $exec     = $this->getExecutionMs();
        $mem      = $this->getMemoryMb();
        $php      = PHP_VERSION;
        $route    = htmlspecialchars($this->currentRoute, ENT_QUOTES, 'UTF-8');

        $rows = '';
        foreach ($queries as $i => $q) {
            $sql    = htmlspecialchars($q['sql'], ENT_QUOTES, 'UTF-8');
            $params = htmlspecialchars(json_encode($q['params'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
            $idx    = $i + 1;
            $ms     = $q['time'];
            $rows .= <<<HTML
            <div class="ief-row">
                <div class="ief-row-head"><span class="ief-idx">#$idx</span><span class="ief-ms">$ms ms</span></div>
                <pre class="ief-sql">$sql</pre>
                <div class="ief-params">params: $params</div>
            </div>
HTML;
        }

        if ($count === 0) {
            $rows = '<div class="ief-empty">Bu istekte SQL sorgusu yok.</div>';
        }

        return <<<HTML
        <style>
            #ief-debug{position:fixed;bottom:0;left:0;right:0;z-index:99999;
                background:#ffffff;border-top:1px solid #e2e8f0;
                color:#0f172a;font:12px/1.4 'Inter',system-ui,sans-serif;
                box-shadow:0 -4px 12px rgba(15,23,42,.06)}
            #ief-debug .ief-head{display:flex;align-items:center;justify-content:space-between;
                padding:10px 18px;cursor:pointer;background:#f8fafc;border-bottom:1px solid transparent}
            #ief-debug .ief-head.open{border-bottom-color:#e2e8f0}
            #ief-debug .ief-brand{font-weight:700;letter-spacing:-.01em;color:#1e40af;font-size:13px}
            #ief-debug .ief-brand .dot{display:inline-block;width:7px;height:7px;background:#06b6d4;border-radius:2px;margin-right:6px;vertical-align:1px}
            #ief-debug .ief-stats{display:flex;gap:18px;font-size:12px;color:#475569}
            #ief-debug .ief-stats b{color:#0f172a;font-weight:600}
            #ief-debug .ief-body{max-height:340px;overflow:auto;display:none;padding:8px 14px;background:#ffffff}
            #ief-debug .ief-body.open{display:block}
            #ief-debug .ief-row{padding:10px 12px;border-bottom:1px solid #f1f5f9}
            #ief-debug .ief-row-head{display:flex;justify-content:space-between;margin-bottom:6px}
            #ief-debug .ief-idx{color:#06b6d4;font-weight:600}
            #ief-debug .ief-ms{color:#1e40af;font-weight:600}
            #ief-debug .ief-sql{margin:0;color:#0f172a;white-space:pre-wrap;word-break:break-word;
                font-family:'SF Mono',ui-monospace,monospace;font-size:12px;background:#f8fafc;padding:8px 10px;border-radius:4px}
            #ief-debug .ief-params{color:#94a3b8;margin-top:4px;font-size:11px;font-family:'SF Mono',ui-monospace,monospace}
            #ief-debug .ief-empty{padding:14px;color:#94a3b8;text-align:center}
        </style>
        <div id="ief-debug">
            <div class="ief-head" onclick="var b=this.nextElementSibling;var o=b.classList.toggle('open');this.classList.toggle('open',o)">
                <div><span class="ief-brand"><span class="dot"></span>ief-framework</span> <span style="opacity:.5;font-size:11px;margin-left:6px">debug bar</span></div>
                <div class="ief-stats">
                    <span>route <b>$route</b></span>
                    <span>sql <b>$count</b></span>
                    <span>time <b>{$exec}ms</b></span>
                    <span>mem <b>{$mem}MB</b></span>
                    <span style="opacity:.5">php $php</span>
                </div>
            </div>
            <div class="ief-body">$rows</div>
        </div>
HTML;
    }
}
