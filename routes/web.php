<?php

use App\Livewire\Auth\Register;
use Livewire\Volt\Volt;
use \Illuminate\Support\Facades\Route;

Volt::route('/', 'users.index');
Route::get('/register', Register::class)->name('register');
