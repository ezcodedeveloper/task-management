<?php

use App\Http\Controllers\API\ApiController ;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


    Route::get('get-quote/', [ApiController::class, 'getQuote'])->name('get.quote');