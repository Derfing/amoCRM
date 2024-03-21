<?php

namespace App\Http\Controllers;

use App\Actions\GetAmoCRMKeys;
use Illuminate\Http\Request;

class AMOController extends Controller
{
    public function getKeys(GetAmoCRMKeys $action)
    {
        $action->handle();
    }
}
