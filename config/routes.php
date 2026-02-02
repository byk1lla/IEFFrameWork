<?php

use App\Core\Router;

// =========================================
// E-Fatura Pro Routes
// =========================================

// Dashboard
Router::get('/', 'DashboardController@index');

// Fatura Routes
Router::get('/fatura', 'FaturaController@index');
Router::get('/fatura/yeni', 'FaturaController@create');
Router::post('/fatura/kaydet', 'FaturaController@store');
Router::get('/fatura/{id}', 'FaturaController@show');
Router::post('/fatura/{id}/gonder', 'FaturaController@send');

// Cari Routes
Router::get('/cari', 'CariController@index');
Router::get('/cari/yeni', 'CariController@create');
Router::post('/cari/kaydet', 'CariController@store');
Router::get('/cari/{id}', 'CariController@show');

// Raporlar
Router::get('/raporlar', 'RaporController@index');

// Ayarlar
Router::get('/ayarlar', 'AyarlarController@index');
Router::post('/ayarlar/kaydet', 'AyarlarController@store');

// Auth
Router::get('/login', 'AuthController@loginForm');
Router::post('/login', 'AuthController@login');
Router::get('/logout', 'AuthController@logout');

// API Endpoints
Router::get('/api/version', function () {
    header('Content-Type: application/json');
    echo json_encode(['version' => '2.0.0', 'app' => 'E-Fatura Pro']);
});

Router::get('/api/cari/ara', 'ApiController@cariAra');
Router::post('/api/vkn/sorgula', 'ApiController@vknSorgula');
Router::get('/api/dashboard/stats', 'ApiController@dashboardStats');
Router::get('/api/fatura/liste', 'ApiController@faturaListesi');
Router::get('/api/fatura/detay/{uuid}', 'ApiController@faturaDetay');
Router::post('/api/mukellef/ara', 'ApiController@mukellefAra');
Router::get('/api/fatura/pdf/{uuid}', 'ApiController@downloadPdf');

