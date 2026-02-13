<div class="alert alert-{{ $type ?? 'info' }}">
    <div class="alert-icon">
        @if(($type ?? 'info') === 'error') ❌ @elseif(($type ?? 'info') === 'success') ✅ @else ℹ️ @endif
    </div>
    <div class="alert-content">
        {{ $message ?? $slot ?? '' }}
    </div>
</div>

<style>
    .alert {
        display: flex;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-radius: 16px;
        margin-bottom: 1rem;
        border-left: 4px solid;
        background: rgba(255, 255, 255, 0.03);
    }

    .alert-error {
        border-color: #ef4444;
        color: #fca5a5;
    }

    .alert-success {
        border-color: #10b981;
        color: #6ee7b7;
    }

    .alert-info {
        border-color: #6366f1;
        color: #a5b4fc;
    }
</style>