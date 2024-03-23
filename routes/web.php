<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AMOController;

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

Route::view('/createLead', 'transaction_form');
Route::post('/createLead', [AMOController::class, 'createLead']);

Route::view('/editLead', 'edit_transaction_form');
Route::post('/editLead', [AMOController::class, 'editLead']);

Route::view('/generateLeadsFile', 'generate_leads_file_form');
Route::get('/downloadLeadsFile', [AMOController::class, 'downloadLeadsFile']);

Route::view('/importLeadsFile', 'import_leads_file');
Route::post('/importLeadsFile', [AMOController::class, 'importLeadsFile']);
