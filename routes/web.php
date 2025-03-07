<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('/bijaya', function () {
    return Inertia::render('bijaya');
})->name('bijaya');

Route::get('/check-pr', function () {
    $service = new \App\Services\GithubService;
    $response = $service->fetchPRDiff('laravel', 'framework', 1);

    return $response->json();
});

Route::get('/chat', [ChatController::class, 'index'])->name('chat');
Route::get('/api/chat', [ChatController::class, 'chat'])->name('api.chat');

Route::get('/simple-sse', function () {
    return response()->stream(function () {
        for ($i = 0; $i < 5; $i++) {
            echo "data: Hello $i\n\n";
            ob_flush();
            flush();
            sleep(1);
        }
        echo "data: </stream>\n\n";
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
