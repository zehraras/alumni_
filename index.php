<?php

// Alumni Management System - Week 02 Routing
// Request method and path parsing
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';

// Parse path without query string
$path = parse_url($requestUri, PHP_URL_PATH);

// Support both direct root routes and /alumni prefix (e.g. /alumni/hello)
if (str_starts_with($path, '/alumni')) {
    $path = substr($path, strlen('/alumni'));
    if ($path === '' || $path === false) {
        $path = '/';
    }
}

// Normalize trailing slash (except for root '/')
if (strlen($path) > 1 && str_ends_with($path, '/')) {
    $path = rtrim($path, '/');
}

// Ensure GET method for the defined routes
if ($method !== 'GET') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

// Step 1: GET / -> "ok"
if ($path === '/') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "ok";
    exit;
}

// Step 2: GET /hello -> "Hello, World!"
if ($path === '/hello') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Hello, World!";
    exit;
}

// Step 3: GET /hello/{name} -> "Hello, {Name}!" (e.g. /hello/emre -> "Hello, Emre!")
if (preg_match('#^/hello/([^/]+)$#', $path, $matches)) {
    header('Content-Type: text/plain; charset=utf-8');
    $rawName = urldecode($matches[1]);
    $formattedName = ucfirst($rawName);
    echo "Hello, {$formattedName}!";
    exit;
}

// Step 4: GET /sum/{number1}/{number2} -> returns sum of number1 and number2
if (preg_match('#^/sum/([^/]+)/([^/]+)$#', $path, $matches)) {
    header('Content-Type: text/plain; charset=utf-8');
    $num1 = $matches[1];
    $num2 = $matches[2];

    if (is_numeric($num1) && is_numeric($num2)) {
        $sum = $num1 + $num2;
        echo $sum;
    } else {
        http_response_code(400);
        echo "Error: Parameters must be numbers";
    }
    exit;
}

// 404 Fallback
http_response_code(404);
echo "404 Not Found";
