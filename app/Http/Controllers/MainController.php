<?php

namespace App\Http\Controllers;

use App\Client;
use App\Core\Notifications\PushNotification;
use App\Core\Notifications\SmscApi;
use App\Excursion;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use RuntimeException;

class MainController extends Controller
{
    public function index()
    {
        return view('index');
    }
}
