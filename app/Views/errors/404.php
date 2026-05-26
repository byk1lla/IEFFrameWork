@extends('layouts.app')

@section('title', '404 — Sayfa Bulunamadı')

@section('content')
<div style="text-align:center;padding:80px 20px">
    <h1 style="font-size:96px;margin:0;background:linear-gradient(135deg,#06B6D4,#8B5CF6);
               -webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:800">404</h1>
    <h2 style="font-size:24px;margin:8px 0 16px">Sayfa Bulunamadı</h2>
    <p style="color:rgba(15,23,42,.6)">{{ $message ?? 'Aradığınız sayfa mevcut değil.' }}</p>
    <a href="/" style="margin-top:20px;display:inline-block;padding:10px 24px;
                      border:1px solid rgba(6,182,212,.3);border-radius:6px;color:#06B6D4">Anasayfa</a>
</div>
@endsection
