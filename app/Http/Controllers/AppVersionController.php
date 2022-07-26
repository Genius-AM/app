<?php

namespace App\Http\Controllers;

use App\AppVersion;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class AppVersionController extends Controller
{
    /**
     * @return array|Factory|View|mixed
     */
    public function index()
    {
        $version = AppVersion::first();

        return view('admin.app_version', compact('version'));
    }

    /**
     * @return array|Factory|View|mixed
     */
    public function edit()
    {
        $version = AppVersion::first();

        return view('admin.app_version_show', compact('version'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Redirector|void
     */
    public function update(Request $request)
    {
        $version = AppVersion::first();
        $version->version = $request->input('version');
        $version->save();

        return redirect(route('admin.app_version'));
    }
}
