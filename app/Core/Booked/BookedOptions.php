<?php


namespace App\Core\Booked;
use App\BookedTime;
use App\Cars;
use App\Category;
use App\Excursion;
use App\Http\Controllers\API\Managers\ManagerController;
use App\Route;
use App\UsersDeviceToken;
use Carbon\Carbon;

class BookedOptions
{
    /**
     * Универсальная функция на противоположное значение брони
     * @param $category_id
     * @param $subcategory_id
     * @param $route_id
     * @param $date
     * @param $time
     * @return |null
     */
    public function changeOrAddBookData($category_id, $subcategory_id, $route_id, $date, $time)
    {
        $booked_item = BookedTime::where('route_id', $route_id)
            ->where('date', $date)
            ->where('time', $time)
            ->first();

        $result = null;
        if(!empty($booked_item)){
            $booked_state = $booked_item->booked == 0 ? 1 : 0;
            $result = $this->createOrUpdateBookedTime($category_id, $subcategory_id, $route_id, $date, $time, $booked_item->id, $booked_state);
        } else {
            $result = $this->createOrUpdateBookedTime($category_id, $subcategory_id, $route_id, $date, $time);
        }

        return $result;
    }

    /**
     * @param $category_id
     * @param $subcategory_id
     * @param $route_id
     * @param $date
     * @param $time
     * @return bool|null
     */
    public function closeBookData($category_id, $subcategory_id, $route_id, $date, $time)
    {
        $booked_item = BookedTime::where('route_id', $route_id)
            ->where('date', $date)
            ->where('time', $time)
            ->first();

        $result = null;

        if (!empty($booked_item)) {
            $result = $this->createOrUpdateBookedTime($category_id, $subcategory_id, $route_id, $date, $time, $booked_item->id);
        } else {
            $result = $this->createOrUpdateBookedTime($category_id, $subcategory_id, $route_id, $date, $time);
        }

        return $result;
    }

    /**
     * Создать или отредактировать бронь
     *
     * @param $category_id
     * @param $subcategory_id
     * @param $route_id
     * @param $date
     * @param $time
     * @param null $id
     * @param int $booked_state
     * @return bool
     */
    public function createOrUpdateBookedTime($category_id, $subcategory_id, $route_id, $date, $time, $id = null, $booked_state = 1)
    {
        $new_booked_time = '';
        if(!empty($id)) $new_booked_time = BookedTime::find($id);
        else $new_booked_time = new BookedTime();

        $new_booked_time->category_id = $category_id;
        $new_booked_time->subcategory_id = $subcategory_id;
        $new_booked_time->route_id = $route_id;
        $new_booked_time->date = $date;
        $new_booked_time->time = $time;
        $new_booked_time->booked = $booked_state;
        $result = $new_booked_time->save();

        return $result;
    }

    /**
     * Возвращает все времена по категории и даты
     * @param $date
     * @param $category_id
     * @return array|bool
     */
    public function getAllTimesDataFromCategory($date, $category_id)
    {
        $weekday = strtolower(Carbon::createFromFormat('Y-m-d', $date)->locale('en_En')->isoFormat('dddd'));
        $all_times = Category::where('id', $category_id)
            ->with(['subcategories.routes.days' => function($query) use($weekday){
                $query->where('weekday', $weekday)
                    ->with('times');
            }])->first();

        $result_all_times = [];
        if(count($all_times->subcategories) > 0){
            if(count($all_times->subcategories[0]->routes) > 0){
                /** @var Route $value */
                foreach($all_times->subcategories[0]->routes as $key => $value){
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
                        'category_id'       => $all_times->id,
                        'subcategory_id'    => $all_times->subcategories[0]->id,
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

        return $result_all_times;
    }

    /**
     * Вернут данные в нормальное сортировке, в одном ключе, сортировать по времени
     *
     * @param $data
     * @return array
     */
    public function getNormalDataToDispatcher($data)
    {
        $result = [];
        $last_result = [];
        if (!empty($data) ){
            //собираем сортированный по времени массив
            foreach($data as $key => $value){
                if( count($value['times']) > 0 ){
                    foreach($value['times'] as $k => $v){
                        $result[$v['time']][] = [
                            'category_id'       => $value['category_id'],
                            'subcategory_id'    => $value['subcategory_id'],
                            'route_id'          => $value['route_id'],
                            'route_name'        => $value['route_name'],
                            'day'               => $value['day'],
                            'date'              => $value['date'],
                            'time_id'           => $v['id'],
                            'time'              => $v['time'],
                            'booked'            => $v['booked'],
                            'amount'            => (new ManagerController())->getAmount($value['route_id'], $value['date'], $v['time'], null, false),
                        ];
                    }
                } else continue;
            }
            //убираем ключи
            if(!empty( $result )){
                ksort($result, SORT_NUMERIC);
                foreach($result as $iKey => $iValue){
                    foreach($iValue as $iK => $iV){
                        $last_result[] = $iV;
                    }
                }
                return $last_result;
            } return [];
        } else return [];
    }

    /**
     * Комбинирует все маршруты с их бронью
     * @param $all_times
     * @return bool
     */
    public function combineAllDataWithBookedTimes($all_times)
    {
        if(!empty($all_times)){
            foreach($all_times as $key => $value){
                if(!empty($value['times'])){
                    $booked_time = BookedTime::where('route_id', $value['route_id'])
                        ->where('date', $value['date'])
                        ->get();
                    if(count($booked_time)>0){
                        foreach($value['times'] as $k => $v){
                            $all_times[$key]['times'][$k]['booked'] = $this->checkForExistTimeInBookedData($booked_time, $v['time']);
                        }
                    } else {
                        foreach($value['times'] as $k => $v){
                            $all_times[$key]['times'][$k]['booked'] = 0;
                        }
                    }
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }

        return $all_times;
    }

    /**
     * Ищем вхождение в массиве значение
     * @param $search_array
     * @param $needle
     * @return int
     */
    private function checkForExistTimeInBookedData($search_array, $needle)
    {
        foreach ($search_array as $key => $value){
            if( $value->time == $needle) return $value->booked;
        }
        return 0;
    }
}