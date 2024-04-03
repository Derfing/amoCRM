<?php

namespace App\Http\Controllers;

use App\Actions\GetAmoCRMKeys;
use App\Services\AmoCRMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AMOController extends Controller
{
    public function getKeys(GetAmoCRMKeys $action)
    {
        $action->handle();
    }

    public function createlead(AmoCRMService $service, Request $request)
    {
        $service->createLead($request);
    }

    public function editlead(AmoCRMService $service, Request $request)
    {
        $service->editLead($request);
    }

    public function downloadLeadsFile(AmoCRMService $service)
    {
        return $service->downloadLeadsFile();
    }

    public function importLeadsFile(AmoCRMService $service, Request $request)
    {
        $file = $request->file('json_data');
        $service->importLeadsFile($file->get());
    }

    public function createCall(AmoCRMService $service, Request $request)
    {
        $service->createCall();
    }
}
