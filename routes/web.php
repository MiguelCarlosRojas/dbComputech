<?php

use App\Http\Controllers\DetectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DetectionController::class, 'index'])->name('detection.index');
Route::post('/api/detections', [DetectionController::class, 'store'])->name('detection.store');
Route::get('/api/detections', [DetectionController::class, 'logs'])->name('detection.logs');
Route::delete('/api/detections', [DetectionController::class, 'clear'])->name('detection.clear');
Route::get('/api/camera/stream-proxy', [DetectionController::class, 'streamProxy'])->name('camera.streamProxy');
Route::get('/api/camera/snapshot-proxy', [DetectionController::class, 'snapshotProxy'])->name('camera.snapshotProxy');
