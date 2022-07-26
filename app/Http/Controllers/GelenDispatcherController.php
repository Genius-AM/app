<?php

namespace App\Http\Controllers;

use App\Excursion;
use App\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GelenDispatcherController extends Controller {

    /**
     * @param Request $request
     * @param $mainCategory
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
	public function getAllOrdersForTheCategory(Request $request, $mainCategory)
    {
		return view('orders--new', compact('mainCategory'));
	}

    /**
     * @param Request $request
     * @param Order $order
     * @param Excursion $excursion
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
	public function cancelOrder(Request $request, Order $order, Excursion $excursion) : JsonResponse
    {
		$order->status_id = 1;
		$order->refuser_id = $request->user()->id;
		$order->save();
		$excursion->people = (int)$excursion->people - $order->peopleSum();
		$excursion->save();
		$excursion->orders()->detach($order->id);
		if ($excursion->people === 0) {
			$excursion->delete();
		}

		return response()->json(['success' => 200, 'message' => 'Заявка была отменена']);
	}
}
