<?php

// File: api/index.php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * This file acts as the entry point for Vercel serverless functions.
 * It bootstraps the Laravel application.
 */

define('LARAVEL_START', microtime(true));

// Turn on output buffering to prevent unexpected output
ob_start();

// Require the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap the Laravel application
$app = require_once __DIR__.'/../bootstrap/app.php';

// Make the HTTP kernel from the application container
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Handle the incoming HTTP request
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Send the response back to the browser and clean the output buffer
$response->send();
ob_end_flush();

// Terminate the application
$kernel->terminate($request, $response);