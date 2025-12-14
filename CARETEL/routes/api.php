<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PenugasanApiController;

Route::apiResource('penugasan', PenugasanApiController::class);