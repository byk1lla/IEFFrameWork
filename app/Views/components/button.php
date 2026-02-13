<button class="btn btn-{{ $type ?? 'primary' }} {{ $class ?? '' }}" id="{{ $id ?? '' }}">
    {!! $slot ?? $label ?? 'Button' !!}
</button>

<style>
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        font-family: inherit;
    }

    .btn-primary {
        background: #6366f1;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.05);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
    }
</style>