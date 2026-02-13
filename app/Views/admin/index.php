@extends('layouts.admin')

@section('content')
<div class="stat-grid">
    @foreach($stats as $key => $val)
    @include('components.card', ['title' => ucfirst($key), 'content' => $val])
    @endforeach
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    @include('components.card', ['title' => 'Sistem Durumu', 'content' => 'Tüm servisler normal çalışıyor.'])
    @include('components.card', ['title' => 'Son Aktiviteler', 'content' => 'Giriş yapıldı: 12 dk önce'])
</div>
@endsection