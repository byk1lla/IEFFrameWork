@extends('layouts.app')

@section('content')
<style>
    .v4-hero {
        text-align: center;
        padding: 100px 0 150px;
        position: relative;
    }

    .titan-badge {
        display: inline-block;
        padding: 8px 24px;
        border-radius: 100px;
        background: rgba(139, 92, 246, 0.05);
        border: 1px solid var(--border);
        font-weight: 800;
        font-size: 0.75rem;
        letter-spacing: 3px;
        color: var(--purple);
        text-shadow: var(--glow);
        margin-bottom: 40px;
        text-transform: uppercase;
    }

    .v4-hero h1 {
        font-size: 8rem;
        font-weight: 900;
        line-height: 0.85;
        letter-spacing: -6px;
        margin-bottom: 40px;
        text-transform: uppercase;
    }

    .v4-gradient {
        background: linear-gradient(135deg, #fff 30%, var(--purple) 60%, var(--cyan) 90%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 0 30px rgba(139, 92, 246, 0.2));
    }

    .v4-hero p {
        max-width: 700px;
        margin: 0 auto 60px;
        font-size: 1.4rem;
        color: var(--text-dim);
        font-weight: 500;
        line-height: 1.4;
    }

    .v4-cta {
        display: flex;
        gap: 24px;
        justify-content: center;
    }

    .btn-v4-outline {
        padding: 20px 50px;
        border-radius: 4px;
        border: 1px solid var(--border);
        background: rgba(255, 255, 255, 0.02);
        color: #fff;
        text-decoration: none;
        font-weight: 800;
        font-size: 1rem;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-v4-outline:hover {
        background: rgba(139, 92, 246, 0.1);
        border-color: var(--purple);
        box-shadow: var(--glow);
    }

    /* Titan Grid */
    .titan-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
        margin-top: 50px;
        padding-bottom: 100px;
    }

    .t-card {
        background: var(--obsidian);
        border: 1px solid var(--border);
        padding: 50px;
        border-radius: 4px;
        transition: all 0.4s;
        position: relative;
        overflow: hidden;
    }

    .t-card:hover {
        transform: translateY(-10px);
        border-color: var(--purple);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), var(--glow);
    }

    .t-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--purple), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s;
    }

    .t-card:hover::before {
        transform: translateX(100%);
    }

    .t-icon {
        font-size: 3rem;
        margin-bottom: 30px;
        filter: grayscale(1) brightness(2);
    }

    .t-card:hover .t-icon {
        filter: none;
    }

    .t-card h3 {
        font-size: 1.8rem;
        font-weight: 900;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: -0.5px;
    }

    .t-card p {
        color: var(--text-dim);
        font-size: 1rem;
        line-height: 1.8;
    }

    /* Code Matrix */
    .v4-matrix {
        background: #000;
        border: 1px solid var(--border);
        padding: 60px;
        border-radius: 8px;
        margin-top: 100px;
        font-family: 'JetBrains Mono', monospace;
        position: relative;
        box-shadow: inset 0 0 50px rgba(139, 92, 246, 0.05);
    }

    .m-header {
        position: absolute;
        top: -1px;
        left: 50%;
        transform: translateX(-50%);
        padding: 5px 20px;
        border: 1px solid var(--border);
        border-top: none;
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--purple);
        background: var(--bg);
        border-radius: 0 0 8px 8px;
        letter-spacing: 2px;
    }

    .m-line {
        margin-bottom: 12px;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .m-kw {
        color: var(--purple);
        font-weight: 700;
    }

    .m-fn {
        color: var(--cyan);
    }

    .m-str {
        color: #10b981;
    }

    .m-comment {
        color: #334155;
    }

    @media (max-width: 1100px) {
        .v4-hero h1 {
            font-size: 5rem;
            letter-spacing: -3px;
        }

        .titan-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="v4-hero">
    <div class="titan-badge">{{ trans('platform_badge') }}</div>
    <h1>{!! trans('hero_title', ['simplicity' => '<span class="v4-gradient">' . trans('hero_power') . '</span>']) !!}
    </h1>
    <p>{{ trans('hero_subtitle') }}</p>

    <div class="v4-cta">
        <a href="/docs" class="btn-v4">{{ trans('start_building') }}</a>
        <a href="/blog" class="btn-v4-outline">{{ trans('explore_blog') }}</a>
    </div>

    <div class="v4-matrix">
        <div class="m-header">{{ trans('engine_core_label') }}</div>
        <div class="m-line"><span class="m-comment">// Initialize Titanium Matrix</span></div>
        <div class="m-line"><span class="m-kw">Router</span>::<span class="m-fn">get</span>(<span
                class="m-str">'/'</span>, <span class="m-str">'Titan@launch'</span>);</div>
        <div class="m-line"><span class="m-kw">Model</span>::<span class="m-fn">where</span>(<span
                class="m-str">'power'</span>, <span class="m-str">'unlimited'</span>)-><span class="m-fn">get</span>();
        </div>
    </div>
</div>

<div class="titan-grid">
    <div class="t-card">
        <div class="t-icon">⚡</div>
        <h3>{{ trans('zen_engine') }}</h3>
        <p>{{ trans('zen_engine_desc') }}</p>
    </div>
    <div class="t-card">
        <div class="t-icon">🌌</div>
        <h3>{{ trans('nexus_orm') }}</h3>
        <p>{{ trans('nexus_orm_desc') }}</p>
    </div>
    <div class="t-card">
        <div class="t-icon">⚛️</div>
        <h3>{{ trans('nexus_control') }}</h3>
        <p>{{ trans('nexus_control_desc') }}</p>
    </div>
</div>
@endsection