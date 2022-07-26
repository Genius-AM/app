<?php

namespace App\Http\Controllers\Cars;

use App\Cars;
use App\CarsAttachmentHistory;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CarsAttachmentHistoryController extends Controller
{
    /**
     * Закрепление водителя страница
     *
     * @param $id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function attach_page($id)
    {
        $car = Cars::find($id);
        $drivers = User::driver()
            ->where('category_id', '=', $car->category_id)
            ->with('company')
            ->get();

        $current_car_state = Cars::find($id);

        if(!empty($current_car_state)) $car_attachment = $current_car_state->driver_id;
            else $car_attachment = null;

        $attaches = Cars::where('driver_id', '!=', 'null')->get();
        $drivers = self::search_driver($drivers, $attaches, $car_attachment);

        return view('cars.attach', compact('car','id', 'drivers', 'car_attachment'));
    }

    /**
     * Формирование нового массива водителей (убираем уже занятые)
     *
     * @param $drivers
     * @param $attaches
     * @param $car_attachment_id
     * @return
     */
    static private function search_driver($drivers, $attaches, $car_attachment_id)
    {
        if(!empty($attaches)){
            foreach( $attaches as $key => $value ){
                if($value->driver_id === $car_attachment_id) continue;

                foreach($drivers as $iKey => $iValue){
                    if($value->driver_id === $iValue->id) {
                        unset($drivers[$iKey]);
                    }
                }
            }
        }

        return $drivers;
    }

    /**
     * Закрепление водителя
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function attach(Request $request, int $id): RedirectResponse
    {
        $driver_id = $request->get('driver_id');
        $car = Cars::find($id);

        $car->driver_id = $driver_id;
        $car->save();

        //история\логирование
        self::action($id, $driver_id);

        if (empty($driver_id)) {
            return redirect()
                ->route('admin.cars.cars.index')
                ->with('success', 'Водитель сброшен');
        }

        return redirect()
            ->route('admin.cars.cars.index')
            ->with('success', 'Водитель закреплен');
    }

    /**
     * Вывод истории
     *
     * @param $car_id
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
     */
    public function attach_list($car_id)
    {
        $history = CarsAttachmentHistory::history($car_id)->get();

        foreach ($history as $key => $value){
            $createdAt = Carbon::parse($value['begin_attach']);
            $value['begin_attach'] = $createdAt->format('d-m-Y H:i:s');
            if(!empty($value['end_attach'])){
                $createdAt = Carbon::parse($value['end_attach']);
                $value['end_attach'] = $createdAt->format('d-m-Y H:i:s');
            }
        }

        return view('cars.attach_list_container', compact('history'));
    }
    /**
     * Производство истории
     *
     * @param $car_id
     * @param $driver_id
     */
    static public function action($car_id, $driver_id)
    {
        $history = CarsAttachmentHistory::history($car_id)->first();
        $createdAt = Carbon::now();

        if( !empty($driver_id) ){
            //если driver_id не пустой
            if (empty($history)){
                //если истории нет, создаем новую запись
                self::save_new_history($car_id,$driver_id,$createdAt);
            } else {
                //если есть история
                if( !empty($history->end_attach) ){
                    //не пуста дата окончания, создаем новую запись
                    self::save_new_history($car_id,$driver_id,$createdAt);
                } else {
                    //пуста дата окончания
                    $history->end_attach = $createdAt;
                    $history->save();

                    //сохранение новой записи истории
                    self::save_new_history($car_id,$driver_id,$createdAt);
                }
            }
        } else {
            //если driver_id пустой
            if (!empty($history)){
                //если есть история
                if(empty($history->end_attach)){
                    //если задача еще не закрыта

                    $history->end_attach = $createdAt;
                    $history->save();
                }
            }
        }
    }

    /**
     * Сохранение новой записи истории
     *
     * @param $car_id
     * @param $driver_id
     * @param $createdAt
     */
    static private function save_new_history($car_id, $driver_id, $createdAt)
    {
        $record = new CarsAttachmentHistory();
        $record->car_id = $car_id;
        $record->driver_id = $driver_id;
        $record->begin_attach = $createdAt;
        $record->save();
    }
}
