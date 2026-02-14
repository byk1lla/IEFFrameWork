@extends('layouts.app')

@section('content')
<style>
    .titan-page-header { margin-bottom: 80px; text-align: left; }
    .titan-page-header h1 { 
        font-size: 5rem; font-weight: 900; letter-spacing: -4px; 
        text-transform: uppercase; margin-bottom: 10px;
    }
    .titan-page-header h1 span { color: var(--cyan); text-shadow: 0 0 20px rgba(6, 182, 212, 0.2); }
    .titan-page-header p { font-size: 1.2rem; color: var(--text-dim); font-weight: 500; }

    .titan-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 40px; padding-bottom: 100px; }
    .ex-card-titan { 
        background: var(--obsidian); border: 1px solid var(--border); 
        padding: 60px; border-radius: 4px; transition: all 0.4s; 
        position: relative; overflow: hidden;
    }
    .ex-card-titan:hover { 
        border-color: var(--cyan); box-shadow: 0 0 30px rgba(6, 182, 212, 0.1); 
        transform: translateY(-10px); 
    }
    
    .ex-icon-v4 { font-size: 3rem; margin-bottom: 30px; }
    .ex-card-titan h3 { 
        font-size: 2rem; font-weight: 900; margin-bottom: 20px; 
        text-transform: uppercase; letter-spacing: -0.5px;
    }
    .ex-card-titan p { color: var(--text-dim); font-size: 1.1rem; line-height: 1.8; margin-bottom: 35px; }
    
    .ex-link-v4 { 
        display: inline-block; padding: 15px 35px; border-radius: 4px; 
        background: rgba(6, 182, 212, 0.05); border: 1px solid var(--border); 
        color: #fff; text-decoration: none; font-weight: 800; 
        text-transform: uppercase; letter-spacing: 2px; font-size: 0.8rem;
        transition: all 0.3s;
    }
    .ex-card-titan:hover .ex-link-v4 { background: var(--cyan); color: #000; border-color: var(--cyan); }

    @media (max-width: 900px) {
        .titan-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="titan-page-header">
    <h1>The <span>{{ trans('ecosystem') }}</span></h1>
    <p>Titanium grade templates for the modern elite developer.</p>
</div>

<div class="titan-grid">
    @foreach($examples as $ex)
    <div class="ex-card-titan">
        <div class="ex-icon-v4">{{ $ex['icon'] ?? '⚡' }}</div>
        <h3>{{ $ex['title'] ?? '' }}</h3>
        <p>{{ $ex['description'] ?? '' }}</p>
        <a href="{{ $ex['url'] ?? '#' }}" class="ex-link-v4">Initalize Module →</a>
    </div>
    @endforeach
</div>
@endsection