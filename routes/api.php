<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DesaController;

Route::get('/desas-map', [DesaController::class, 'index']);

