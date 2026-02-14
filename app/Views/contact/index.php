@extends('layouts.app')

@section('content')
<style>
    .contact-stage {
        max-width: 800px;
        margin: 0 auto;
        padding-bottom: 150px;
    }

    .titan-page-header {
        margin-bottom: 80px;
        text-align: center;
    }

    .titan-page-header h1 {
        font-size: 5rem;
        font-weight: 900;
        letter-spacing: -4px;
        text-transform: uppercase;
    }

    .titan-page-header h1 span {
        color: #f472b6;
        text-shadow: 0 0 20px rgba(244, 114, 182, 0.2);
    }

    .titan-form {
        background: var(--obsidian);
        border: 1px solid var(--border);
        padding: 60px;
        border-radius: 4px;
    }

    .form-group-titan {
        margin-bottom: 30px;
    }

    .form-group-titan label {
        display: block;
        font-size: 0.75rem;
        font-weight: 900;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 12px;
    }

    .titan-input {
        width: 100%;
        padding: 20px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid var(--border);
        border-radius: 4px;
        color: #fff;
        font-family: inherit;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .titan-input:focus {
        outline: none;
        border-color: #f472b6;
        background: rgba(244, 114, 182, 0.05);
    }

    .btn-submit-titan {
        width: 100%;
        padding: 22px;
        background: #f472b6;
        border: none;
        border-radius: 4px;
        color: #000;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 3px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-submit-titan:hover {
        transform: scale(1.02);
        box-shadow: 0 0 30px rgba(244, 114, 182, 0.4);
    }

    .success-badge {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid #10b981;
        color: #10b981;
        padding: 20px;
        border-radius: 4px;
        margin-bottom: 40px;
        font-weight: 800;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>

<div class="contact-stage">
    <div class="titan-page-header">
        <h1>{!! trans('contact_title', ['span' => '<span>Link</span>']) !!}</h1>
        <p>{{ trans('contact_subtitle') }}</p>
    </div>

    @if(isset($success))
    <div class="success-badge">{{ trans('contact_success') }}</div>
    @endif

    <form class="titan-form" action="/contact" method="POST">
        <div class="form-group-titan">
            <label>{{ trans('contact_name_label') }}</label>
            <input type="text" name="name" class="titan-input" placeholder="ACCESS_ID" required>
        </div>
        <div class="form-group-titan">
            <label>{{ trans('contact_email_label') }}</label>
            <input type="email" name="email" class="titan-input" placeholder="NODE@NEXUS" required>
        </div>
        <div class="form-group-titan">
            <label>{{ trans('contact_message_label') }}</label>
            <textarea name="message" class="titan-input" style="height: 150px;" placeholder="..." required></textarea>
        </div>
        <button type="submit" class="btn-submit-titan">{{ trans('contact_submit') }}</button>
    </form>
</div>
@endsection