<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/{pathMatch}', function () {
    return view('welcome');
})->where('pathMatch', ".*");



// Npm install
// npm install vue-loader@next vue@next vue-router@next --force
// npm install @vitejs/plugin-vue --force --save-dev
// Npm run dev
