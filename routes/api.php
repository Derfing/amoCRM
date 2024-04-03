<?php

use Makeroi\Amocrm\Services\MakeroiRoute;
use App\Jobs\AddTaskWebhookGlobalProcessJob;
use \App\Http\Middleware\CheckRequestRelevance;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(CheckRequestRelevance::class)->prefix('v0')->group(function ()
{
    MakeroiRoute::webhookGlobal(AddTaskWebhookGlobalProcessJob::class);
});

