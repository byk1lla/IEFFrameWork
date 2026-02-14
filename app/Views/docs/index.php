<!DOCTYPE html>
<html lang="{{ \App\Core\Lang::getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ trans('knowledge') }} | IEF V4</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #030303;
            --obsidian: #080808;
            --purple: #8B5CF6;
            --purple-glow: rgba(139, 92, 246, 0.3);
            --cyan: #06B6D4;
            --cyan-glow: rgba(6, 182, 212, 0.3);
            --text: #f8fafc;
            --text-dim: #94a3b8;
            --border: rgba(139, 92, 246, 0.15);
            --code-bg: #000;
            --surface: rgba(255, 255, 255, 0.02);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.7;
            overflow-x: hidden;
        }

        .titan-doc-layout {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Redesign */
        .doc-sidebar {
            width: 340px;
            border-right: 1px solid var(--border);
            padding: 60px 40px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            background: var(--obsidian);
            scrollbar-width: none;
        }

        .doc-sidebar::-webkit-scrollbar {
            display: none;
        }

        .sidebar-logo {
            font-size: 1.6rem;
            font-weight: 900;
            letter-spacing: 5px;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 60px;
        }

        .sidebar-logo .v4-dot {
            width: 8px;
            height: 8px;
            background: var(--purple);
            border-radius: 50%;
            box-shadow: 0 0 15px var(--purple);
        }

        .doc-nav h3 {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--purple);
            margin: 40px 0 20px;
            font-weight: 900;
            opacity: 0.7;
        }

        .doc-nav a {
            display: block;
            color: var(--text-dim);
            text-decoration: none;
            font-size: 0.95rem;
            margin-bottom: 14px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            padding: 5px 0;
            border-left: 2px solid transparent;
            padding-left: 0;
        }

        .doc-nav a:hover {
            color: #fff;
            padding-left: 15px;
        }

        .doc-nav a.active {
            color: #fff;
            font-weight: 800;
            padding-left: 15px;
            border-left: 2px solid var(--purple);
        }

        /* Content Redesign */
        .doc-stage {
            flex: 1;
            padding: 120px 80px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-to-zero {
            color: var(--cyan);
            text-decoration: none;
            font-weight: 800;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 80px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }

        .back-to-zero:hover {
            text-shadow: 0 0 15px var(--cyan);
        }

        h1 {
            font-size: 7rem;
            font-weight: 950;
            letter-spacing: -6px;
            margin-bottom: 100px;
            text-transform: uppercase;
            line-height: 0.9;
        }

        h1 span {
            color: transparent;
            -webkit-text-stroke: 1px var(--purple);
        }

        .titan-section {
            margin-bottom: 200px;
            scroll-margin-top: 50px;
        }

        .titan-section h2 {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 30px;
            letter-spacing: -2px;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .titan-section h2::after {
            content: '';
            height: 2px;
            flex: 1;
            background: linear-gradient(to right, var(--border), transparent);
        }

        .titan-section p {
            color: var(--text-dim);
            font-size: 1.3rem;
            margin-bottom: 50px;
            max-width: 800px;
        }

        .titan-block {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 50px;
            margin-bottom: 50px;
            position: relative;
            overflow: hidden;
        }

        .titan-block::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--purple);
            box-shadow: 0 0 20px var(--purple);
        }

        .block-tag {
            background: rgba(139, 92, 246, 0.1);
            color: var(--purple);
            padding: 6px 16px;
            border-radius: 4px;
            font-weight: 900;
            font-size: 0.7rem;
            border: 1px solid var(--border);
            margin-bottom: 25px;
            display: inline-block;
            letter-spacing: 2px;
        }

        .block-title {
            font-size: 1.8rem;
            font-weight: 900;
            margin-bottom: 20px;
            text-transform: uppercase;
            color: #fff;
        }

        pre {
            background: var(--code-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 50px;
            overflow-x: auto;
            margin: 50px 0;
            font-family: 'JetBrains Mono', monospace;
            font-size: 1rem;
            line-height: 1.9;
            position: relative;
            box-shadow: inset 0 0 60px rgba(0, 0, 0, 0.5);
        }

        pre::after {
            content: attr(data-label);
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 0.7rem;
            color: #334155;
            font-weight: 800;
            letter-spacing: 3px;
        }

        .kw {
            color: #f472b6;
        }

        .fn {
            color: var(--cyan);
        }

        .str {
            color: #10b981;
        }

        .var {
            color: #facc15;
        }

        .cm {
            color: #475569;
        }

        .lang-v4-fixed {
            position: fixed;
            top: 40px;
            right: 80px;
            display: flex;
            gap: 30px;
            background: var(--obsidian);
            padding: 15px 30px;
            border-radius: 80px;
            border: 1px solid var(--border);
            z-index: 1000;
        }

        .lang-v4-fixed a {
            color: var(--text-dim);
            text-decoration: none;
            font-weight: 900;
            font-size: 0.8rem;
            letter-spacing: 2px;
        }

        .lang-v4-fixed a.active {
            color: var(--purple);
            text-shadow: 0 0 10px var(--purple);
        }

        .insight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .insight-card {
            background: rgba(6, 182, 212, 0.02);
            border: 1px solid rgba(6, 182, 212, 0.1);
            padding: 35px;
            border-radius: 12px;
            position: relative;
            transition: all 0.3s;
        }

        .insight-card:hover {
            border-color: var(--cyan);
            background: rgba(6, 182, 212, 0.05);
        }

        .insight-card h4 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--cyan);
            margin-bottom: 20px;
            font-weight: 900;
        }

        .insight-card p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 0;
            color: #cbd5e1;
        }

        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .specs-table td {
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.9rem;
        }

        .specs-table td:first-child {
            color: var(--purple);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 40%;
        }

        .specs-table td:last-child {
            color: #fff;
            font-family: 'JetBrains Mono', monospace;
        }

        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            background: var(--purple);
            z-index: 2000;
            width: 0%;
            transition: width 0.1s linear;
        }

        @media (max-width: 1024px) {
            .doc-sidebar {
                display: none;
            }

            .doc-stage {
                padding: 80px 40px;
            }

            h1 {
                font-size: 4rem;
            }
        }
    </style>
</head>

<body>
    <div class="scroll-progress" id="progressBar"></div>

    <div class="lang-v4-fixed">
        <a href="/lang/tr" class="{{ \App\Core\Lang::getLocale() === 'tr' ? 'active' : '' }}">TR</a>
        <a href="/lang/en" class="{{ \App\Core\Lang::getLocale() === 'en' ? 'active' : '' }}">EN</a>
    </div>

    <div class="titan-doc-layout">
        <aside class="doc-sidebar">
            <a href="/" class="sidebar-logo">
                <div class="v4-dot"></div>
                TITAN<span>V4</span>
            </a>

            <nav class="doc-nav">
                <h3>{{ trans('foundation') }}</h3>
                <a href="#lifecycle" class="active">{{ trans('doc_lifecycle') }}</a>

                <h3>{{ trans('mechanics') }}</h3>
                <a href="#backbone">{{ trans('doc_backbone') }}</a>
                <a href="#mirror">{{ trans('doc_mirror') }}</a>

                <h3>{{ trans('intelligence') }}</h3>
                <a href="#source">{{ trans('doc_source') }}</a>
                <a href="#gateway">{{ trans('doc_gateway') }}</a>

                <h3>{{ trans('nodes') }}</h3>
                <a href="#nodes">{{ trans('doc_nodes') }}</a>
            </nav>
        </aside>

        <main class="doc-stage">
            <a href="/" class="back-to-zero">← {{ trans('return_to_zero') }}</a>
            <h1 id="lifecycle">{{ trans('knowledge') }}<span>.</span></h1>

            <!-- LIFECYCLE -->
            <section class="titan-section" id="lifecycle">
                <h2>{{ trans('doc_lifecycle') }}</h2>
                <p>{{ trans('doc_lifecycle_desc') }}</p>

                <div class="titan-block">
                    <div class="block-tag">APP.PHP / INDEX.PHP</div>
                    <div class="block-title">{{ trans('doc_singleton_title') }}</div>
                    <p>{{ trans('doc_lifecycle_singleton') }}</p>

                    <table class="specs-table">
                        <tr>
                            <td>Pattern</td>
                            <td>Strict Singleton (Thread-Safe)</td>
                        </tr>
                        <tr>
                            <td>Initialization</td>
                            <td>Lazy Loading (getInstance)</td>
                        </tr>
                        <tr>
                            <td>Binding</td>
                            <td>Application Interface v1.0</td>
                        </tr>
                    </table>
                </div>

                <pre data-label="App::run() Pipeline">
<span class="kw">public function</span> <span class="fn">run</span>(): <span class="kw">void</span>
{
    <span class="cm">// Step 1: Initialize Persistent Storage</span>
    <span class="kw">Session</span>::<span class="fn">start</span>();

    <span class="cm">// Step 2: Protocol Detection (Locale Matrix)</span>
    <span class="kw">Lang</span>::<span class="fn">load</span>();

    <span class="cm">// Step 3: Route Registry & Dispatch Matrix</span>
    <span class="kw">require</span> CONFIG_PATH . <span class="str">'/routes.php'</span>;
    <span class="kw">Router</span>::<span class="fn">dispatch</span>();
}</pre>

                <div class="insight-grid">
                    <div class="insight-card">
                        <h4>{{ trans('doc_insight_lang_50_title') }}</h4>
                        <p>{{ trans('doc_insight_lifecycle_50') }}</p>
                    </div>
                    <div class="insight-card">
                        <h4>{{ trans('doc_insight_router_54_title') }}</h4>
                        <p>{{ trans('doc_insight_lifecycle_54') }}</p>
                    </div>
                </div>
            </section>

            <!-- BACKBONE -->
            <section class="titan-section" id="backbone">
                <h2>{{ trans('doc_backbone') }}</h2>
                <p>{{ trans('doc_backbone_desc') }}</p>

                <div class="titan-block">
                    <div class="block-tag">ROUTER.PHP</div>
                    <div class="block-title">{{ trans('doc_regex_title') }}</div>
                    <p>{{ trans('doc_backbone_regex') }}</p>

                    <table class="specs-table">
                        <tr>
                            <td>Engine</td>
                            <td>PCRE Regex v2.0</td>
                        </tr>
                        <tr>
                            <td>Injection</td>
                            <td>PHP Reflection API</td>
                        </tr>
                        <tr>
                            <td>Protocols</td>
                            <td>GET, POST, PUT, DELETE, PATCH</td>
                        </tr>
                    </table>
                </div>

                <pre data-label="Router::convertToPattern()">
<span class="kw">protected static function</span> <span class="fn">convertToPattern</span>(<span class="kw">string</span> <span class="var">$uri</span>): <span class="kw">string</span>
{
    <span class="var">$pattern</span> = <span class="fn">preg_quote</span>(<span class="var">$uri</span>, <span class="str">'#'</span>);
    
    <span class="kw">foreach</span> (self::<span class="var">$patterns</span> <span class="kw">as</span> <span class="var">$placeholder</span> => <span class="var">$regex</span>) {
        <span class="var">$pattern</span> = <span class="fn">str_replace</span>(<span class="fn">preg_quote</span>(<span class="var">$placeholder</span>, <span class="str">'#'</span>), <span class="var">$regex</span>, <span class="var">$pattern</span>);
    }

    <span class="var">$pattern</span> = <span class="fn">preg_replace</span>(<span class="str">'#\\\\\{([a-zA-Z0-9\_]+)\\\\\}#'</span>, <span class="str">'([^/]+)'</span>, <span class="var">$pattern</span>);
    
    <span class="kw">return</span> <span class="str">'#^'</span> . <span class="var">$pattern</span> . <span class="str">'$#'</span>;
}</pre>

                <div class="insight-card" style="border-color: var(--purple); background: rgba(139, 92, 246, 0.05);">
                    <h4 style="color: var(--purple);">{{ trans('doc_insight_router_54_title') }}</h4>
                    <p>{{ trans('doc_insight_router_54') }}</p>
                </div>
            </section>

            <!-- MIRROR -->
            <section class="titan-section" id="mirror">
                <h2>{{ trans('doc_mirror') }}</h2>
                <p>{{ trans('doc_mirror_desc') }}</p>

                <div class="titan-block">
                    <div class="block-tag">VIEW.PHP</div>
                    <div class="block-title">{{ trans('doc_compiler_title') }}</div>
                    <p>{{ trans('doc_mirror_compiler') }}</p>

                    <table class="specs-table">
                        <tr>
                            <td>Strategy</td>
                            <td>Static Compilation (Pre-Eval)</td>
                        </tr>
                        <tr>
                            <td>Safety</td>
                            <td>HTML Entities + Null Coalescing</td>
                        </tr>
                        <tr>
                            <td>Performance</td>
                            <td>Native Opcache Compatible</td>
                        </tr>
                    </table>
                </div>

                <pre data-label="View::compile()">
<span class="var">$directives</span> = [
    <span class="str">'/(?<!@)\{\{\s*(.+?)\s*\}\}/'</span> => <span class="str">'&lt;?php echo htmlspecialchars($1 ?? ""); ?&gt;'</span>,
    <span class="str">'/(?<!@)@@extends\s*\([\'"](.+?)[\'"]\)/s'</span> => <span class="str">'&lt;?php \App\Core\View::setLayout(\'$1\'); ?&gt;'</span>,
];</pre>

                <div class="insight-card">
                    <h4>{{ trans('doc_insight_mirror_safety_title') }}</h4>
                    <p>{{ trans('doc_insight_mirror_safety') }}</p>
                </div>
            </section>

            <!-- SOURCE -->
            <section class="titan-section" id="source">
                <h2>{{ trans('doc_source') }}</h2>
                <p>{{ trans('doc_source_desc') }}</p>

                <div class="titan-block" style="border-color: var(--cyan); background: rgba(6, 182, 212, 0.05);">
                    <div class="block-tag" style="background: var(--cyan); border-color: var(--cyan); color: #000;">
                        MODEL.PHP</div>
                    <div class="block-title">{{ trans('doc_fluid_api') }}</div>
                    <p>{{ trans('doc_insight_orm_fluid') }}</p>

                    <table class="specs-table">
                        <tr>
                            <td>Architecture</td>
                            <td>Active Record + Fluent Builder</td>
                        </tr>
                        <tr>
                            <td>Primary Key</td>
                            <td>UUID v4 / AUTO_INCREMENT</td>
                        </tr>
                        <tr>
                            <td>Safety</td>
                            <td>Prepared Statements (PDO Bound)</td>
                        </tr>
                    </table>
                </div>

                <pre data-label="Model::query()">
<span class="kw">public function</span> <span class="fn">where</span>(<span class="kw">string</span> <span class="var">$column</span>, <span class="var">$value</span>, <span class="kw">string</span> <span class="var">$operator</span> = <span class="str">'='</span>): <span class="kw">self</span>
{
    <span class="var">$this</span>->wheres[] = <span class="str">"{$column} {$operator} ?"</span>;
    <span class="var">$this</span>->params[] = <span class="var">$value</span>;
    <span class="kw">return</span> <span class="var">$this</span>;
}</pre>

                <div class="insight-card">
                    <h4>{{ trans('doc_insight_orm_uuid_title') }}</h4>
                    <p>{{ trans('doc_insight_orm_uuid') }}</p>
                </div>
            </section>

            <!-- GATEWAY -->
            <section class="titan-section" id="gateway">
                <h2>{{ trans('doc_gateway') }}</h2>
                <p>{{ trans('doc_gateway_desc') }}</p>

                <pre data-label="Controller::view()">
<span class="kw">protected function</span> <span class="fn">view</span>(<span class="kw">string</span> <span class="var">$view</span>, <span class="kw">array</span> <span class="var">$data</span> = []): <span class="kw">Response</span>
{
    <span class="var">$data</span>[<span class="str">'authUser'</span>] = <span class="var">$this</span>->user;
    <span class="var">$data</span>[<span class="str">'csrf_token'</span>] = <span class="kw">Session</span>::<span class="fn">getCsrfToken</span>();
    ...
}</pre>
                <div class="insight-card">
                    <h4>{{ trans('doc_insight_controller_24_title') }}</h4>
                    <p>{{ trans('doc_insight_controller_24') }}</p>
                </div>
            </section>

            <!-- NODES -->
            <section class="titan-section" id="nodes">
                <h2>{{ trans('doc_nodes') }}</h2>
                <p>{{ trans('doc_nodes_desc') }}</p>

                <div class="insight-grid">
                    <div class="insight-card">
                        <h4>{{ trans('doc_node_req_title') }}</h4>
                        <p>{{ trans('doc_node_req_desc') }}</p>
                    </div>
                    <div class="insight-card">
                        <h4>{{ trans('doc_node_sess_title') }}</h4>
                        <p>{{ trans('doc_node_sess_desc') }}</p>
                    </div>
                    <div class="insight-card">
                        <h4>{{ trans('doc_node_log_title') }}</h4>
                        <p>{{ trans('doc_node_log_desc') }}</p>
                    </div>
                </div>
            </section>

            <footer
                style="margin-top: 150px; padding-top: 50px; border-top: 1px solid var(--border); color: var(--text-dim); font-size: 0.75rem; letter-spacing: 2px; text-transform: uppercase;">
                {{ trans('knowledge_base_footer') }}
            </footer>
        </main>
    </div>

    <script>
        const sections = document.querySelectorAll('.titan-section');
        const navLinks = document.querySelectorAll('.doc-nav a');
        const progress = document.getElementById('progressBar');

        window.addEventListener('scroll', () => {
            // Progress Bar
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            progress.style.width = scrolled + "%";

            // Scrollspy
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                if (window.pageYOffset >= sectionTop - 250) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').includes(current) && current !== '') {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>

</html>