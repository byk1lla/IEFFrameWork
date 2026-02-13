<div class="premium-card {{ $class ?? '' }}">
    @if(isset($title))
    <div class="card-header">
        <h3>{{ $title }}</h3>
    </div>
    @endif
    <div class="card-body">
        {!! $slot ?? $content ?? '' !!}
    </div>
</div>

<style>
    .premium-card {
        background: rgba(17, 24, 39, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        padding: 2rem;
        transition: all 0.3s;
    }

    .premium-card:hover {
        border-color: rgba(99, 102, 241, 0.4);
        transform: translateY(-4px);
    }

    .card-header h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #fff;
    }

    .card-body {
        color: #9ca3af;
        line-height: 1.6;
    }
</style>