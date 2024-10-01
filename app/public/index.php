<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Kernel; // Add this if you need the Kernel

$request = Request::createFromGlobals();

// Prepare the routing setup
$context = new RequestContext();
$context->fromRequest($request);

// Create a route collection
$routes = new RouteCollection();

// Load routes from the YAML file
$locator = new FileLocator([__DIR__ . '/../config/routes']);
$loader = new YamlFileLoader($locator);

$routes = $loader->load('routes.yaml');

// Create a matcher
$matcher = new UrlMatcher($routes, $context);

try {
    // Match the request to a route
    $attributes = $matcher->match($request->getPathInfo());
    $controllerClass = $attributes['_controller'];

    // Instantiating the controller class
    [$className, $method] = explode('::', $controllerClass);
    $controller = new $className();

    // Calling the controller method
    $response = call_user_func([$controller, $method]);
} catch (ResourceNotFoundException $e) {
    $response = new Response('Page not found', 404);
} catch (Exception $e) {
    $response = new Response('An error occurred: ' . $e->getMessage(), 500);
}

// Send the response
$response->send();
