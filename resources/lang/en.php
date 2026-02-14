<?php

return [
    'hero_title' => 'Unrivaled <br>:simplicity',
    'hero_power' => 'Power.',
    'hero_subtitle' => 'Experience the absolute pinnacle of performance and precision. A PHP platform designed for the elite who value minimalist excellence and pure speed.',
    'start_building' => 'Initialize System',
    'explore_blog' => 'Explore Blog',
    'zen_engine' => 'Titan Engine',
    'zen_engine_desc' => 'A template engine that fades into the background, leaving only pure logic and power.',
    'nexus_orm' => 'Obsidian ORM',
    'nexus_orm_desc' => 'Fluid, object-oriented database interactions that feel like natural language.',
    'nexus_control' => 'Control Titan',
    'nexus_control_desc' => 'The ultimate administrator cockpit. Rebuilt for absolute command over your infrastructure.',
    'nexus_gateway' => 'Nexus Gateway',
    'back_to_nexus' => 'Return to Nexus',
    'knowledge' => 'Knowledge',
    'ecosystem' => 'Ecosystem',
    'experience' => 'Experience',
    'admin_dashboard' => 'Titan Dashboard',
    'status_ready' => 'Status: Obsidian Ready',
    'control_center' => 'Control Center',
    'administrator' => 'Titan Overseer',

    // V4 Additional Keys
    'blog_title' => 'TitanBlog',
    'blog_subtitle' => 'Inside the Obsidian Matrix. Thoughts on Elite Architecture.',
    'read_more' => 'Read Mission Log',
    'back_to_blog' => 'Abort Mission (Back to Blog)',
    'mission_by' => 'MISSION BY:',
    'contact_title' => 'Titan:span',
    'contact_subtitle' => 'Establish a secure connection with the system administrators.',
    'contact_name_label' => 'Identification (Name)',
    'contact_email_label' => 'Matrix Uplink (Email)',
    'contact_message_label' => 'Deep Message',
    'contact_submit' => 'Transmit Data',
    'contact_success' => 'Communication Established Successfully.',
    'system_status' => 'SYSTEM STATUS: 100% TITANIUM',
    'blog_desc_placeholder' => 'Loading intelligence...',
    'platform_badge' => 'IEF PLATFORM V4 • OBSIDIAN CORE',
    'engine_core_label' => 'TITAN ENGINE CORE',
    'return_to_zero' => 'Return To Zero',
    'foundation' => 'Foundation Layer',
    'mechanics' => 'Core Mechanics',
    'visualization' => 'Visualization',
    'intelligence' => 'Intelligence',
    'knowledge_base_footer' => 'Titan V4 Knowledge Base • Final Grade Clearance Required • 2026',
    'philosophy_subtitle' => 'Experience the absolute pinnacle of performance and precision.',

    // THE LIFECYCLE (Quantum Detail)
    'doc_lifecycle' => 'System Execution Lifecycle',
    'doc_lifecycle_desc' => 'The boot process of Titan V4 is a deterministic sequence. It begins when the web server points to `public/index.php`, where the `App` instance is awakened.',
    'doc_lifecycle_singleton' => 'The `App` class utilizes a Strict Singleton pattern. During `run()`, it sequentially initializes the Session, loads User-defined configuration, detects Locale, and finally binds the Router to the execution context.',
    'doc_insight_lifecycle_50' => 'Localization is session-persistent. `Lang::load()` looks for the `locale` key; if absent, it defaults to TR but instantly can be hot-swapped via the `/lang/{locale}` route without data loss.',
    'doc_insight_lifecycle_54' => 'The `Router::dispatch()` call is the "Point of No Return". It captures global server vars to determine which slice of the application needs to be executed.',

    // THE BACKBONE (Quantum Detail)
    'doc_backbone' => 'The Routing Matrix',
    'doc_backbone_desc' => 'Routing in IEF is not just path mapping; it is a complex Regex interpretation system. It supports Method Overriding, enabling browsers to send PUT and DELETE requests.',
    'doc_backbone_regex' => 'Variables like `{id}` are captured via `preg_match`. The system then uses PHP Reflection to inject these parameters directly into your Controller methods with zero configuration.',
    'doc_insight_router_54_title' => 'Method Overriding Logic',
    'doc_insight_router_54' => 'If a POST request contains a `_method` field (or `X-HTTP-Method-Override` header), the Router re-identifies the request as PUT or DELETE at runtime.',

    // THE MIRROR (Quantum Detail)
    'doc_mirror' => 'Titan Directive Engine',
    'doc_mirror_desc' => 'The Mirror (View) doesn\'t interpret; it compiles. It sweeps the `.php` files, detects IEF directives, and replaces them with pure, optimized PHP code.',
    'doc_mirror_compiler' => 'Every `{{ }}` is wrapped in `htmlspecialchars` and a null-coalescing operator `?? ""`. This ensures that even if you pass a null variable, the UI never breaks (Null-Safety).',
    'doc_insight_mirror_safety_title' => 'The Eval Context',
    'doc_insight_mirror_safety' => 'Compiled content is executed via `eval()`. Before execution, all passed data is extracted using `extract($data)`, turning array keys into live variables.',

    // THE SOURCE (Quantum Detail)
    'doc_source' => 'Obsidian ORM Architecture',
    'doc_source_desc' => 'Obsidian is a fluent query builder. It uses "Lazy Building"—queries are prepared in memory and only executed when a terminator method like `get()` or `first()` is called.',
    'doc_insight_orm_fluid' => 'Each `where()` call appends to an internal array. The final SQL is constructed only at the last millisecond to ensure maximum performance.',
    'doc_insight_orm_uuid_title' => 'UUID Generation Logic',
    'doc_insight_orm_uuid' => 'If `$useUuid` is true, the `Model::create()` method utilizes `Symfony/Uid` to generate an RFC 4122 compliant UUID before the INSERT command hits the database.',

    // THE GATEWAY (Quantum Detail)
    'doc_gateway' => 'Controller & Protocol Logic',
    'doc_gateway_desc' => 'Controllers are not just logic bins—they are protected nodes. They extend the base class to gain access to the simplified Response and Validation wrappers.',
    'doc_insight_controller_24_title' => 'Dependency Injection',
    'doc_insight_controller_24' => 'The Router analyzes your method parameters. If it sees a `Request` type-hint, it automatically injects the singleton Request instance through Reflection.',

    // NODES & STATS
    'doc_nodes' => 'System Node Matrix',
    'doc_nodes_desc' => 'The smaller, yet vital organs that maintain systemic equilibrium:',
    'doc_node_req_title' => 'Request Discovery',
    'doc_node_req_desc' => 'Automatically detects JSON payloads and parses them into the input stream. Supports the HTMX header protocol out of the box.',
    'doc_node_sess_title' => 'Session Entropy',
    'doc_node_sess_desc' => 'Manages CSRF tokens and flash messages. Tokens are regenerated on every state-changing request to prevent replay attacks.',
    'doc_node_log_title' => 'Logger Integrity',
    'doc_node_log_desc' => 'Writes atomic log files. Supports severity levels (DEBUG, INFO, ERROR). Logs are rotated daily in the storage directory.',

    // UI LABELS
    'doc_singleton_title' => 'Singleton Execution',
    'doc_regex_title' => 'Regex Parameter Capture',
    'doc_compiler_title' => 'Directive Compilation Pipeline',
    'doc_fluid_api' => 'The Fluid Interface',
    'doc_insight_lang_50_title' => 'Language Hot-Swapping',
    'doc_insight_label' => 'Technical Insight',
];
