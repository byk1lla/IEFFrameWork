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
                <div style='padding:12px 20px;border-bottom:1px solid rgba(255,255,255,0.03);font-family:\"JetBrains Mono\", monospace;'>
                    <div style='color:var(--cyan, #06B6D4);font-size:11px;font-weight:700;margin-bottom:4px;'>SQL EXECUTION</div>
                    <div style='color:#fff;font-size:12px;opacity:0.9;line-height:1.4;word-break:break-all;'>{$q['sql']}</div>
                    <div style='display:flex;gap:15px;margin-top:8px;'>
                        <span style='color:rgba(255,255,255,0.4);font-size:10px;text-transform:uppercase;letter-spacing:1px;'>Latency: <b style='color:#fff'>{$q['time']}</b></span>
                    </div>
                </div>";
        }

        return <<<HTML
        <!-- Titan Pulse Debugger -->
        <style>
            #titan-pulse {
                position: fixed;
                bottom: 20px;
                right: 20px;
                left: 20px;
                background: rgba(5, 5, 5, 0.85);
                backdrop-filter: blur(24px) saturate(180%);
                border: 1px solid rgba(139, 92, 246, 0.3);
                border-radius: 8px;
                z-index: 99999;
                font-family: 'Outfit', sans-serif;
                color: #fff;
                box-shadow: 0 10px 40px rgba(0,0,0,0.8), 0 0 20px rgba(139, 92, 246, 0.1);
                overflow: hidden;
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .pulse-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 25px;
                height: 50px;
                cursor: pointer;
                background: linear-gradient(90deg, rgba(139, 92, 246, 0.1), transparent);
            }
            .pulse-stats {
                display: flex;
                gap: 25px;
                font-size: 11px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 1.5px;
            }
            .pulse-stats span b {
                color: #06B6D4;
                margin-left: 4px;
            }
            .pulse-content {
                display: none;
                max-height: 400px;
                overflow-y: auto;
                border-top: 1px solid rgba(255,255,255,0.05);
            }
            .pulse-content::-webkit-scrollbar { width: 4px; }
            .pulse-content::-webkit-scrollbar-thumb { background: rgba(139, 92, 246, 0.3); }
        </style>
        <div id="titan-pulse">
            <div class="pulse-header" onclick="let c = document.getElementById('pulse-body'); c.style.display = c.style.display === 'block' ? 'none' : 'block'">
                <div style="display:flex;align-items:center;gap:15px;">
                    <span style="font-size:18px;filter:drop-shadow(0 0 8px #8B5CF6)">⚡</span>
                    <span style="font-weight:900;letter-spacing:2px;font-size:12px;">TITAN <span style="color:#06B6D4">PULSE</span> V5</span>
                </div>
                <div class="pulse-stats">
                    <div style="display:flex;flex-direction:column;gap:2px;">
                        <span style="font-size:8px;color:rgba(255,255,255,0.3)">LATENCY</span>
                        <span><b>{$execTime}</b></span>
                        <div style="width:100%;height:2px;background:rgba(255,255,255,0.05);border-radius:1px;">
                            <div style="width:min(100%, calc(({$execTime} / 200) * 100%));height:100%;background:#06B6D4;box-shadow:0 0 5px #06B6D4;"></div>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:2px;">
                        <span style="font-size:8px;color:rgba(255,255,255,0.3)">MEMORY</span>
                        <span><b>{$memory}</b></span>
                        <div style="width:100%;height:2px;background:rgba(255,255,255,0.05);border-radius:1px;">
                            <div style="width:min(100%, calc(({$memory} / 32) * 100%));height:100%;background:#8B5CF6;box-shadow:0 0 5px #8B5CF6;"></div>
                        </div>
                    </div>
                    <span>Route: <b>{$this->currentRoute}</b></span>
                    <span>SQL: <b>{$queryCount}</b></span>
                    <span style="opacity:0.5;margin-left:auto">PHP v{$phpVersion}</span>
                </div>
            </div>
            <div id="pulse-body" class="pulse-content">
                <div style="padding:15px 25px;background:rgba(255,255,255,0.02);font-weight:900;font-size:10px;letter-spacing:2px;color:rgba(255,255,255,0.3);text-transform:uppercase;border-bottom:1px solid rgba(255,255,255,0.05);">Matrix Query Logs</div>
                {$itemsHtml}
            </div>
        </div>
HTML;
    }
}
