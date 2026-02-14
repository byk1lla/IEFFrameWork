<?php

use App\Core\Router;

// Dashboard / Welcome
Router::get('/', 'WelcomeController@index');

// Example Task Routes
Router::get('/tasks', 'TaskController@index');
Router::post('/tasks/create', 'TaskController@store');
Router::post('/tasks/{id}/update', 'TaskController@update');
Router::post('/tasks/{id}/delete', 'TaskController@delete');

// Auth System (Titan Guard)
Router::get('/login', 'AuthController@showLogin');
Router::post('/login', 'AuthController@login');
Router::get('/register', 'AuthController@showRegister');
Router::post('/register', 'AuthController@register');
Router::get('/logout', 'AuthController@logout');

// Error Reporting API
Router::post('/api/report-error', 'ErrorReporterController@report');

// Admin Dashboard
Router::get('/admin', 'AdminController@index', ['middleware' => \App\Middleware\AuthMiddleware::class]);

// Documentation
Router::get('/docs', function () {
    return \App\Core\View::render('docs.index');
});

// Examples Hub
Router::get('/examples', 'ExampleController@index');


// Contact Hub
Router::get('/contact', 'ContactController@index');
Router::post('/contact', 'ContactController@submit');

// Language Switch
Router::get('/lang/{locale}', function ($locale) {
    \App\Core\Lang::setLocale($locale);
    back();
});