<?php

use App\Core\Router;

// Welcome Route
Router::get('/', 'WelcomeController@index');

// Example API Route
Router::get('/api/version', function() {
    header('Content-Type: application/json');
    echo json_encode(['version' => '1.0.0', 'framework' => 'IEF Framework']);
});

// Task Routes
Router::get('/tasks', 'TaskController@index');
Router::post('/tasks/create', 'TaskController@store');
Router::post('/tasks/{id}/update', 'TaskController@update');
Router::post('/tasks/{id}/delete', 'TaskController@delete');
