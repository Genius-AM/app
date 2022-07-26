<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;

class GelenDriverController extends Controller {

    /**
     * @return JsonResponse
     */
	public function index()
    {
		$drivers = User::driver()
            ->orderBy('name', 'asc')
            ->get();

		return response()->json($drivers);
	}
}
