<?php
/**
 * Uygulama Route'ları
 *
 * @package IEF Framework
 */

use App\Core\Router;

/*
 * Kurulum Sihirbazı (storage/installed.lock yoksa erişilebilir)
 */
Router::get ('/install',          'InstallController@index');
Router::get ('/install/database', 'InstallController@database');
Router::post('/install/database', 'InstallController@database');
Router::get ('/install/migrate',  'InstallController@migrate');
Router::post('/install/migrate',  'InstallController@migrate');
Router::get ('/install/admin',    'InstallController@admin');
Router::post('/install/admin',    'InstallController@admin');
Router::get ('/install/site',     'InstallController@site');
Router::post('/install/site',     'InstallController@site');
Router::get ('/install/done',     'InstallController@done');

/*
 * SEO & PWA (statik dosyalar gibi)
 */
Router::get('/sitemap.xml',          'SeoController@sitemap');
Router::get('/robots.txt',           'SeoController@robots');
Router::get('/manifest.webmanifest', 'PwaController@manifest');

/*
 * Welcome / Anasayfa
 */
Router::get('/', 'WelcomeController@index');

/*
 * Error Reporter — exception sayfasındaki "Hatayı Raporla" butonu
 */
Router::post('/api/report-error', 'ErrorReporterController@report');

/*
 * Dokümantasyon
 */
Router::get('/docs',          'DocsController@index');
Router::get('/docs/{topic}',  'DocsController@show');

/*
 * İletişim
 */
Router::get ('/iletisim',             'ContactController@index');
Router::post('/iletisim',             'ContactController@submit');
Router::get ('/iletisim/tesekkurler', 'ContactController@thanks');

/*
 * Blog
 */
Router::get('/blog',         'BlogController@index');
Router::get('/blog/{slug}',  'BlogController@show');

/*
 * Randevu (public)
 */
Router::get ('/randevu',              'AppointmentController@index');
Router::post('/randevu',              'AppointmentController@book');
Router::get ('/randevu/tesekkurler',  'AppointmentController@thanks');

/*
 * Auth
 */
Router::get ('/login',  'AuthController@showLogin');
Router::post('/login',  'AuthController@login');
Router::get ('/logout', 'AuthController@logout');

Router::get ('/sifre-sifirla',          'AuthController@showForgot');
Router::post('/sifre-sifirla',          'AuthController@sendReset');
Router::get ('/sifre-sifirla/{token}',  'AuthController@showReset');
Router::post('/sifre-sifirla/{token}',  'AuthController@performReset');

/*
 * Public Analytics event endpoint
 */
Router::post('/admin/analytics/event', 'Admin\AnalyticsController@event');

/*
 * Site Editor — session bayrağı aç/kapa (admin login zorunlu)
 */
Router::get('/site-editor', function () {
    if (!\App\Core\SiteContent::isAdminLoggedIn()) { redirect('/login'); return; }
    \App\Core\SiteContent::startEditing();
    redirect('/');
});
Router::get('/site-editor/cikis', function () {
    \App\Core\SiteContent::stopEditing();
    $back = $_SERVER['HTTP_REFERER'] ?? '/';
    if (str_contains($back, '/site-editor')) $back = '/';
    redirect($back);
});
Router::post('/site-editor/appearance/save', 'Admin\ContentEditorController@appearanceSave');

/*
 * Admin
 */
Router::group([
    'prefix'     => '/admin',
    'middleware' => \App\Middleware\AuthMiddleware::class,
], function () {
    Router::get ('/',                          'AdminController@index');

    // Mesajlar
    Router::get ('/messages',                  'Admin\MessageController@index');
    Router::get ('/messages/{id}',             'Admin\MessageController@show');
    Router::post('/messages/{id}/delete',      'Admin\MessageController@destroy');

    // Loglar
    Router::get ('/logs',                      'Admin\LogController@index');

    // Medya
    Router::get ('/media',                     'Admin\MediaController@index');
    Router::post('/media',                     'Admin\MediaController@store');
    Router::post('/media/{id}/delete',         'Admin\MediaController@destroy');

    // Blog
    Router::get ('/blog',                      'Admin\BlogController@index');
    Router::get ('/blog/create',               'Admin\BlogController@create');
    Router::post('/blog',                      'Admin\BlogController@store');
    Router::get ('/blog/{id}/edit',            'Admin\BlogController@edit');
    Router::post('/blog/{id}/update',          'Admin\BlogController@update');
    Router::post('/blog/{id}/delete',          'Admin\BlogController@destroy');
    Router::post('/blog/ai/generate',          'Admin\BlogController@aiGenerate');

    // Site Editör — session bayrağını aç + anasayfaya dön
    Router::get ('/editor', function () {
        \App\Core\SiteContent::startEditing();
        redirect('/');
    });

    // Manuel blok CRUD (legacy — page_blocks tablosu)
    Router::get ('/editor/blocks',                   'Admin\EditorController@blocks');
    Router::get ('/editor/blocks/create',            'Admin\EditorController@create');
    Router::post('/editor/blocks',                   'Admin\EditorController@store');
    Router::get ('/editor/blocks/{id}/edit',         'Admin\EditorController@edit');
    Router::post('/editor/blocks/{id}/update',       'Admin\EditorController@update');
    Router::post('/editor/blocks/{id}/delete',       'Admin\EditorController@destroy');

    // Inline editör endpoint'leri (site_content — Onur)
    Router::post('/editor/save',                     'Admin\ContentEditorController@update');
    Router::post('/editor/upload',                   'Admin\ContentEditorController@uploadImage');

    // Randevular
    Router::get ('/appointments',              'Admin\AppointmentController@index');
    Router::get ('/appointments/{id}',         'Admin\AppointmentController@show');
    Router::post('/appointments/{id}/status',  'Admin\AppointmentController@updateStatus');
    Router::post('/appointments/{id}/delete',  'Admin\AppointmentController@destroy');

    // Kullanıcılar
    Router::get ('/users',                     'Admin\UserController@index');
    Router::get ('/users/create',              'Admin\UserController@create');
    Router::post('/users',                     'Admin\UserController@store');
    Router::get ('/users/{id}/edit',           'Admin\UserController@edit');
    Router::post('/users/{id}/update',         'Admin\UserController@update');
    Router::post('/users/{id}/delete',         'Admin\UserController@destroy');

    // Ayarlar
    Router::get ('/settings',                  'Admin\SettingsController@index');
    Router::get ('/settings/{tab}',            'Admin\SettingsController@index');
    Router::post('/settings',                  'Admin\SettingsController@save');

    // Trafik (Analytics)
    Router::get ('/analytics',                 'Admin\AnalyticsController@index');
    Router::get ('/analytics/requests',        'Admin\AnalyticsController@requests');
    Router::get ('/analytics/events',          'Admin\AnalyticsController@events');
    Router::get ('/analytics/sessions',        'Admin\AnalyticsController@sessions');
    Router::get ('/analytics/sessions/{id}',   'Admin\AnalyticsController@sessionDetail');

    // Hata Raporları
    Router::get ('/error-reports',             'Admin\ErrorReportController@index');
    Router::get ('/error-reports/{id}',        'Admin\ErrorReportController@show');
    Router::post('/error-reports/{id}/fix',    'Admin\ErrorReportController@fix');
    Router::post('/error-reports/{id}/delete', 'Admin\ErrorReportController@destroy');
    Router::post('/error-reports/clear-fixed', 'Admin\ErrorReportController@clear');
});

/*
 * Dil
 */
Router::get('/lang/{locale}', function ($locale) {
    \App\Core\Lang::setLocale($locale);
    back();
});
