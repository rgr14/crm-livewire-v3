<?php

use App\Livewire\Auth\Register;
use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Volt::route('/', 'users.index');
Route::get('/register', Register::class)->name('register');
Route::get('/logout', fn() => Auth::logout());
