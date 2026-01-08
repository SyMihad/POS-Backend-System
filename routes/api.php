<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'resolve.tenant'])->group(function () {
    // protected routes
    //test route that return test
    Route::get('/test', function (Request $request) {
        return response()->json(['message' => 'Tenant resolved successfully']);
    });
});
