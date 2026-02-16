<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/run-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return 'Storage linked successfully.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
