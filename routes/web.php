<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/pump-control', function () {
    return view('filament.widgets.pump-control');
});
