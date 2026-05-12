<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DeptController;

    Route::get('/', function () {
        return view('welcome');
    });

    Route::get('/login',[DeptController::class,'viewLogin'])->name('login');
    Route::get('/register',[DeptController::class,'viewRegister'])->name('register');
    Route::post('/login/submit',[DeptController::class,'loginSubmit'])->name('login.submit');
    Route::post('/register/submit',[DeptController::class,'registerSubmit'])->name('register.submit');
    

    Route::middleware(['auth'])->group(function () {
        Route::get('/home',[DeptController::class,'home'])->name('home');
        Route::post('/add-person',[DeptController::class,'addPerson'])->name('add.person');
        Route::post('/add-utang',[DeptController::class,'addUtang'])->name('add.utang');
        Route::get('/person-utang/{personId}',[DeptController::class,'getPersonUtang'])->name('person.utang');
        Route::post('/pay-utang/{utangId}',[DeptController::class,'payUtang'])->name('pay.utang');
        Route::get('/get-person',[DeptController::class,'getPerson'])->name('get.person');
        Route::get('/products',[DeptController::class,'viewProducts'])->name('products.index');
        Route::post('/products',[DeptController::class,'addProduct'])->name('products.store');
        Route::post('/sales',[DeptController::class,'addSale'])->name('sales.store');
        Route::get('/sales',[DeptController::class,'viewSales'])->name('sales.index');
        Route::get('/logout',[DeptController::class,'logout'])->name('logout');

    });

