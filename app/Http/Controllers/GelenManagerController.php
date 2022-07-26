<?php

namespace App\Http\Controllers;

use App\Category;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GelenManagerController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
	public function index(Request $request)
    {
        switch ( $request->input('catId')) {
            case Category::DJIPPING:
                $managers = $this->djippingManagers($request);
                break;
            case Category::QUADBIKE:
                $managers = $this->quadbikeManagers($request);
                break;
            case Category::SEA:
                $managers = $this->seaManagers($request);
                break;
            case Category::DIVING:
                $managers = $this->divingManagers($request);
                break;
            default:
                throw new Exception("Unknown");
        }

		return response()->json($managers);
	}

    /**
     * Возврат менеджеров
     *
     * @return JsonResponse
     */
	public function getAll() : JsonResponse
    {
		$managers = User::manager()
            ->orderBy('name', 'asc')
            ->get();

		return response()->json($managers);
	}

    /**
     * @param Request $request
     * @return User[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
	private function djippingManagers(Request $request)
    {
        $managers = User::manager()
            ->with(
                [
                    'orders' => function ($query) use ($request) {
                        $q = $query->whereIn('status_id', [1, 5, 8])
                            ->orderBy('date','asc')
                            ->orderBy('time','asc')
                            ->with('route')
                            ->with('client')
                            ->where('category_id', $request->user()->category_id);
                        if ($request->filled('catId')):
                            $q->where('category_id', $request->input('catId'));
                        endif;
                        if ($request->filled('route')):
                            $q->where('route_id', $request->input('route'));
                        endif;
                        if ($request->filled('date')):
                            $q->where('date', $request->input('date'));
                        endif;
                        if ($request->input('time')):
                            $time_array = explode(":", $request->input('time'));
                            $time = Carbon::createFromTime($time_array[0], $time_array[1])->format('H:i');
                            $q->where('time', 'like', $time.'%');
                        endif;
                    },
                    'orders.point'
                ]
            )
            ->byManager($request->input('managerId'))
            ->get();

        $managers = $this->sortByPoint($managers);
        $managers = $this->createTasks($managers);

        return $managers;
    }

    /**
     * @param Request $request
     * @return User[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
	private function quadbikeManagers(Request $request)
    {
        $managers = User::manager()
            ->with(
                [
                    'orders' => function ($query) use ($request) {
                        $q = $query
                            ->orderBy('date','asc')
                            ->orderBy('time','asc')
                            ->with('route')
                            ->with('client');
                        if ($request->input('catId')):
                            $q->where('category_id', $request->input('catId'));
                        endif;
                        if ($request->filled('route')):
                            $q->where('route_id', $request->input('route'));
                        endif;
                        if ($request->filled('date')):
                            $q->where('date', $request->input('date'));
                        endif;
                        if ($request->input('time')):
                            $time_array = explode(":", $request->input('time'));
                            $time = Carbon::createFromTime($time_array[0], $time_array[1])->format('H:i');
                            $q->where('time', 'like', $time.'%');
                        endif;
                    },
                    'orders.point',
                    'orders.company'
                ]
            )
            ->byManager($request->input('managerId'))
            ->get();

        $managers = $this->sortByPoint($managers);
        $managers = $this->createTasks($managers);

        return $managers;
    }

    /**
     * @param Request $request
     * @return User[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
	private function seaManagers(Request $request)
    {
        $managers = User::manager()
            ->with(
                [
                    'orders' => function ($query) use ($request) {
                        $q = $query
                            ->orderBy('date','asc')
                            ->orderBy('time','asc')
                            ->with('route')
                            ->with('client');
                        if ($request->input('catId')):
                            $q->where('category_id', $request->input('catId'));
                        endif;
                        if ($request->filled('route')):
                            $q->where('route_id', $request->input('route'));
                        endif;
                        if ($request->filled('date')):
                            $q->where('date', $request->input('date'));
                        endif;
                        if ($request->input('time')):
                            $time_array = explode(":", $request->input('time'));
                            $time = Carbon::createFromTime($time_array[0], $time_array[1])->format('H:i');
                            $q->where('time', 'like', $time.'%');
                        endif;
                    },
                    'orders.point'
                ]
            )
            ->byManager($request->input('managerId'))
            ->get();

        $managers = $this->sortByPoint($managers);
        $managers = $this->createTasks($managers);

        return $managers;
    }

    /**
     * @param Request $request
     * @return User[]|Builder[]|Collection|\Illuminate\Support\Collection
     */
	private function divingManagers(Request $request)
    {
        $managers = User::manager()
            ->with(
                [
                    'orders' => function ($query) use ($request) {
                        $q = $query
                            ->orderBy('date','asc')
                            ->orderBy('time','asc')
                            ->with('route')
                            ->with('client');
                        if ($request->input('catId')):
                            $q->where('category_id', $request->input('catId'));
                        endif;
                        if ($request->filled('route')):
                            $q->where('route_id', $request->input('route'));
                        endif;
                        if ($request->filled('date')):
                            $q->where('date', $request->input('date'));
                        endif;
                        if ($request->input('time')):
                            $time_array = explode(":", $request->input('time'));
                            $time = Carbon::createFromTime($time_array[0], $time_array[1])->format('H:i');
                            $q->where('time', 'like', $time.'%');
                        endif;
                    },
                    'orders.point'
                ]
            )
            ->byManager($request->input('managerId'))
            ->get();

        $managers = $this->sortByPoint($managers);
        $managers = $this->createTasks($managers);

        return $managers;
    }


    /**
     * @param \Illuminate\Support\Collection $managers
     * @return \Illuminate\Support\Collection
     */
    private function createTasks(\Illuminate\Support\Collection $managers)
    {
        //для DnD создаем tasks
        foreach ($managers as $key => $value) {
            if (!empty($value['orders'])) {
                $managers[$key]['tasks'] = $value['orders'];
            }
        }

        return $managers;
    }

    /**
     * @param Collection $managers
     * @return \Illuminate\Support\Collection
     */
    private function sortByPoint(Collection $managers)
    {
        $test = collect();
        while ($managers->count()) {
            $manager = $managers->pop();
            if ($manager->orders->count()) {
                $group = $manager->orders->groupBy('point_id');
                /** @var \Illuminate\Support\Collection $orders */
                foreach ($group as $orders) {
                    $manager = $manager->toArray();
                    $manager['point'] = $orders->toArray()[0]['point']['name'];
                    $manager['orders'] = $orders->toArray();
                    $manager = collect($manager);
                    $test->prepend($manager);
                }
            } else {
                $test->prepend($manager);
            }
        }

        return $test;
    }
}
