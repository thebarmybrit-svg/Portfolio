<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const BASE_PATH = __DIR__.'/../';

function base_path($path) {
    return BASE_PATH . $path;
}

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Instantiate the Router BEFORE loading routes
$router = new \Core\Router();

// Load the routes (this injects the definitions into the $router instance above)
require base_path('routes.php');

// Parse the incoming request URL and Method
$uri = parse_url($_SERVER['REQUEST_URI'])['path'];

// Normalize the URI for local subfolder setups
$base_folder = '/Netmatters Homepage/public';
if (str_starts_with($uri, $base_folder)) {
    $uri = substr($uri, strlen($base_folder));
}

// Ensure it always defaults to a trailing slash or single slash
$uri = '/' . trim($uri, '/');

$method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'];

// Dispatch the route
$router->route($uri, $method);