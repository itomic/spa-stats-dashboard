<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('/', function () {
    return view('dashboard');
});

// Serve build assets with CORS headers
// Use Request to get the full path including slashes
Route::get('/build/{path?}', function (Illuminate\Http\Request $request) {
    // Get the full request path after /build/
    $fullPath = $request->path(); // e.g., "build/assets/dashboard.js"
    $path = substr($fullPath, 6); // Remove "build/" prefix
    
    if (empty($path)) {
        abort(404);
    }
    
    $filePath = public_path('build/' . $path);
    
    // Security: prevent directory traversal
    $realPath = realpath($filePath);
    $buildPath = realpath(public_path('build'));
    
    if (!$realPath || !$buildPath || strpos($realPath, $buildPath) !== 0) {
        abort(404);
    }
    
    if (!file_exists($filePath) || !is_file($filePath)) {
        abort(404);
    }
    
    $mimeType = mime_content_type($filePath);
    
    return Response::file($filePath, [
        'Content-Type' => $mimeType,
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        'Access-Control-Allow-Headers' => 'Content-Type',
    ]);
})->where('path', '.*');
