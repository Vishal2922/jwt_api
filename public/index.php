<?php
// 1. Autoload classes (Trainees basic version) 
spl_autoload_register(function ($class) {
    $paths = ['app/controllers/', 'app/models/', 'app/middleware/', 'app/helpers/', 'app/core/'];
    foreach ($paths as $path) {
        $file = __DIR__ . '/../' . $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// 2. Global Middleware (Runs before Routing) 
// Ethu incoming JSON-ai parse panni $_POST['body']-la vaikum
$jsonMiddleware = new JsonMiddleware();
$jsonMiddleware->handle();

// 3. Initialize Router 
$router = new Router();

/**
 * Part 3 - Authentication Routes 
 */
$router->add('POST', '/api/register', 'AuthController', 'register'); 
$router->add('POST', '/api/login', 'AuthController', 'login');
/**
 * Part 5 - Protected Patient Module 
 * Ethula 'AuthMiddleware' use aagurathaala, valid JWT token illama access panna mudiyathu
 */
$router->add('GET', '/api/patients', 'PatientController', 'index', ['AuthMiddleware']); 
$router->add('GET', '/api/patients/{id}', 'PatientController', 'show', ['AuthMiddleware']);
$router->add('POST', '/api/patients', 'PatientController', 'store', ['AuthMiddleware']); 

// Fixed: Dynamic ID logic for Update and Delete 
// Inga {id} add pannurathaala Router-ala dynamic IDs-ai extract panna mudiyum
$router->add('PUT', '/api/patients/{id}', 'PatientController', 'update', ['AuthMiddleware']);
$router->add('DELETE', '/api/patients/{id}', 'PatientController', 'destroy', ['AuthMiddleware']); 

// 4. Dispatch Request 
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Router request-ai match panni controller-ai call pannum 
$router->dispatch($uri, $method);