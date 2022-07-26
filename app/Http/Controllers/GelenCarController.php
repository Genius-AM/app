<?php

namespace App\Http\Controllers;

use App\Cars;
use App\Excursion;
use App\ExcursionOrder;
use App\Order;
use App\Route;
use Illuminate\Http\Request;

class GelenCarController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function getCarsByDate(Request $request)
    {
        $excur_amount = (int)\Config::get('constants.excursions_amount');
        $date = $request->input('date');

        $cars = Cars::where('owner', 'our')
            ->where('category_id', 1)
            ->where('active', true)
            ->get();
        if(!empty($date)){
            foreach($cars as $key => $value){
                $orders = Excursion::where('car_id', $value->id)
                    ->where('date', $date)
                    ->whereIn('status_id', [2, 3, 6, 4, 5, 8])
                    ->get();

                if(count($orders) > 0){
                    $new_orders = [];
                    //собераем сначало отказные
                    foreach($orders as $ker => $valer){
                        if($valer->status_id == 5 or $valer->status_id == 8){
                            $new_orders[] = $valer;
                            unset($orders[$ker]);
                        }
                    }
                    foreach($orders as $ker => $valer){
                        $new_orders[] = $valer;
                    }


                    $new_race_number = 1;
                    $tasks = [];

                    foreach( $new_orders as $iKey => $iValue ){
                        //получить ордеры для текущей экскурсии
                        $orders = [];
                        $orders_parent = ExcursionOrder::where('excursion_id',$iValue->id)->get();
                        foreach($orders_parent as $iiiKey => $iiiValue){
                            $order = Order::where('id', $iiiValue->order_id)->with('point')->first();
                            $order->route = Route::find($order->route_id);
                            $orders[] = $order;
                        }
                        $tasks[] = [
                            'name'      => $new_race_number . ' рейс',
                            'tasks'     => $orders,
                            'excursion' => $iValue
                        ];
                        $new_race_number++;
                    }

                    if ($new_race_number <= $excur_amount) {
                        for($new_race_number; $new_race_number <= $excur_amount; $new_race_number++){
                            $tasks[] = [
                                'name'  => $new_race_number . ' рейс',
                                'tasks' => []
                            ];
                        }
                    }
                    $cars[$key]['tasks'] = $tasks;
                } else {
                    $excursions_amount = [];
                    for($i = 1; $i <= $excur_amount; $i++){
                        $excursions_amount[] = [
                            'name'  =>  $i.' рейс',
                            'tasks' => []
                        ];
                    }
                    $cars[$key]['tasks'] = $excursions_amount;
                }
            }
        } else {
            foreach($cars as $key => $value){
                $excursions_amount = [];
                for($i = 1; $i <= $excur_amount; $i++){
                    $excursions_amount[] = [
                        'name'  =>  $i.' рейс',
                        'tasks' => []
                    ];
                }
                $cars[$key]['tasks'] = $excursions_amount;
            }
        }

		return response()->json($cars);
	}

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
	public function getPartnerCarsByDate(Request $request)
    {
        $date = $request->input('date');

        $cars = Cars::where('owner', 'partner')
            ->where('category_id', 1)
            ->get();
        if(!empty($date)){
            foreach($cars as $key => $value){
                $orders = Excursion::where('car_id', $value->id)
                    ->where('date', $date)
                    ->whereIn('status_id', [2,3,6])
                    ->get();
                if(count($orders) > 0){
                    $new_race_number = 1;
                    $tasks = [];

                    foreach( $orders as $iKey => $iValue ){
                        //получить ордеры для текущей экскурсии
                        $orders = [];
                        $orders_parent = ExcursionOrder::where('excursion_id',$iValue->id)->get();
                        foreach($orders_parent as $iiiKey => $iiiValue){
                            $order = Order::where('id', $iiiValue->order_id)->with('point')->first();
                            $order->route = Route::find($order->route_id);
                            $orders[] = $order;
                        }
                        $tasks[] = [
                            'name'      => $new_race_number . ' рейс',
                            'tasks'     => $orders,
                            'excursion' => $iValue
                        ];
                        $new_race_number++;
                    }

                    if($new_race_number <= 1){
                        for($new_race_number; $new_race_number <= 1; $new_race_number++){
                            $tasks[] = [
                                'name'  => $new_race_number . ' рейс',
                                'tasks' => []
                            ];
                        }
                    }
                    $cars[$key]['tasks'] = $tasks;
                } else {
                    $excursions_amount = [];
                    for($i = 1; $i <= 1; $i++){
                        $excursions_amount[] = [
                            'name'  =>  $i.' рейс',
                            'tasks' => []
                        ];
                    }
                    $cars[$key]['tasks'] = $excursions_amount;
                }
            }
        } else {
            foreach($cars as $key => $value){
                $excursions_amount = [];
                for($i = 1; $i <= 1; $i++){
                    $excursions_amount[] = [
                        'name'  =>  $i.' рейс',
                        'tasks' => []
                    ];
                }
                $cars[$key]['tasks'] = $excursions_amount;
            }
        }

		return response()->json($cars);
	}
}
