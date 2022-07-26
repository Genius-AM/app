<?php

namespace App\Http\Controllers;

use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TimesController extends Controller
{
    /**
     * Возвращаем времена по конкретной дате и категории
     *
     * @param Request $request
     * @return bool
     */
    public function getTimesByDate(Request $request)
    {
        $date = $request->input('date');

        $weekday = strtolower( Carbon::parse($date)->locale('en_En')->isoFormat('dddd') );
        $data = Category::where('id', $request->input('category_id'))
            ->with(['subcategories.routes.days' => function($query) use($weekday){
                $query->where('weekday', $weekday)
                    ->with('times');
            }])->first();

        $result_all_times = [];
        if(count($data->subcategories) > 0){
            if(count($data->subcategories[0]->routes) > 0){
                foreach($data->subcategories[0]->routes as $key => $value){
                    $times = [];
                    if(count($value->days) > 0){
                        if(count($value->days[0]->times) > 0){
                            foreach($value->days[0]->times as $k => $val){
                                $times[] = [
                                    'id'    => $val->id,
                                    'time'  => $val->name,
                                ];
                            }
                        } else continue;
                    }  else continue;

                    $result_all_times[] = [
                        'category_id'       => $data->id,
                        'subcategory_id'    => $data->subcategories[0]->id,
                        'route_id'          => $value->id,
                        'route_name'        => $value->name,
                        'day'               => $weekday,
                        'date'              => $date,
                        'times'             => $times
                    ];
                }
            } else {
                //Нету маршрутов
                return false;
            }
        } else {
            //Нету подкатегорий
            return false;
        }

        //собираем в один массив
        $result = [];
        foreach($result_all_times as $iKey => $iValue){
            if(!empty($iValue['times'])){
                foreach($iValue['times'] as $iK => $iV){
                    $result[] = $iV['time'];
                }
            }
        }
        $result = array_unique($result);
        sort($result, SORT_NUMERIC);

        return response()->json($result, 200);
    }
}
