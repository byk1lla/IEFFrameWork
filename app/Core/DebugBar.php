<?php

namespace App\Core;

/**
 * Developer Debug Bar for IEF Framework
 */
class DebugBar
{
    protected static ?self $instance = null;
    protected array $queries = [];
    protected float $startTime;
    protected array $logs = [];

    protected string $currentRoute = '';

    private function __construct()
    {
        $this->startTime = microtime(true);
    }

    public function setRoute(string $route): void
    {
        $this->currentRoute = $route;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function logQuery(string $sql, array $params, float $time): void
    {
        $this->queries[] = [
            'sql' => $sql,
            'params' => $params,
            'time' => round($time * 1000, 2) . 'ms'
        ];
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getExecutionTime(): string
    {
        return round((microtime(true) - $this->startTime) * 1000, 2) . 'ms';
    }

    public function getMemoryUsage(): string
    {
        return round(memory_get_peak_usage() / 1024 / 1024, 2) . ' MB';
    }

    public function render(): string
    {
        if (!ExceptionHandler::isDebug())
            return '';

        $queries = $this->getQueries();
        $queryCount = count($queries);
        $execTime = $this->getExecutionTime();
        $memory = $this->getMemoryUsage();
        $phpVersion = PHP_VERSION;

        $itemsHtml = '';
        foreach ($queries as $q) {
            $itemsHtml .= "
                <div style='padding:8px;border-bottom:1px solid rgba(255,255,255,0.05);font-family:monospace;font-size:12px;'>
                    <div style='color:#818cf8;'>{$q['sql']}</div>
                    <div style='color:#6b7280;font-size:10px;margin-top:4px;'>Time: {$q['time']}</div>
                </div>";
        }

        return <<<HTML
        <!-- IEF Debug Bar -->
        <div id="ief-debug-bar" style="position:fixed;bottom:0;left:0;right:0;background:rgba(10,10,15,0.95);backdrop-filter:blur(20px);border-top:1px solid rgba(255,255,255,0.1);z-index:999999;font-family:sans-serif;color:#ccc;user-select:none;">
            <div style="display:flex;align-items:center;padding:0 20px;height:40px;font-size:12px;cursor:pointer;" onclick="document.getElementById('ief-debug-content').style.display = document.getElementById('ief-debug-content').style.display === 'none' ? 'block' : 'none'">
                <div style="font-weight:bold;color:#00D1FF;display:flex;align-items:center;">
                    <span style="font-size:16px;margin-right:8px;">⚡</span> IEF DEBUG
                </div>
                <div style="margin-left:20px;display:flex;gap:20px;">
                    <span>📍 <b style="color:#fff">{$this->currentRoute}</b></span>
                    <span>⏱️ <b style="color:#fff">{$execTime}</b></span>
                    <span>💾 <b style="color:#fff">{$memory}</b></span>
                    <span>🗄️ <b style="color:#fff">{$queryCount} Queries</b></span>
                    <span>⚙️ PHP <b style="color:#fff">{$phpVersion}</b></span>
                </div>
            </div>
            <div id="ief-debug-content" style="display:none;max-height:400px;overflow-y:auto;background:rgba(0,0,0,0.5);border-top:1px solid rgba(255,255,255,0.05);padding:10px;">
                <div style="font-weight:bold;padding:10px;color:#fff;border-bottom:1px solid rgba(255,255,255,0.1);">SQL Queries</div>
                {$itemsHtml}
                <div style="padding:10px;font-size:11px;color:#555;text-align:center;">IEF Framework Debug Bar © 2026</div>
            </div>
        </div>
HTML;
    }
}
