<?php

use App\Core\Router;

// Dashboard / Welcome
Router::get('/', 'WelcomeController@index');

// Example Task Routes
Router::get('/tasks', 'TaskController@index');
Router::post('/tasks/create', 'TaskController@store');
Router::post('/tasks/{id}/update', 'TaskController@update');
Router::post('/tasks/{id}/delete', 'TaskController@delete');

// Auth (Optional for generic framework, but keeping if needed for examples)
// Router::get('/login', 'AuthController@loginForm');
// Router::post('/login', 'AuthController@login');
// Router::get('/logout', 'AuthController@logout');

// Error Reporting API
Router::post('/api/report-error', 'ErrorReporterController@report');

// Admin Dashboard
Router::get('/admin', 'AdminController@index');

// Documentation
Router::get('/docs', function () {
    return \App\Core\View::render('docs.index');
});

// Examples Hub
Router::get('/examples', 'ExampleController@index');