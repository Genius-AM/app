<?php

namespace App\Http\Controllers\Cars;

use App\Cars;
use App\Category;
use App\Http\Requests\Lists\Car\StoreRequest;
use App\Http\Requests\Lists\Category\UpdateRequest;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CarsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $cars = Cars::orderBy('order', 'asc')
            ->with('category', 'driver.company')
            ->get();

        return view('cars.index',compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $categories = Category::all();

        return view('cars.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $record = new Cars;
        $record->name = $request->get('name');
        $record->car_number = $request->get('car_number');

        $owner = $request->get('owner');
        $record->passengers_amount = $owner === 'partner' ? 9999 : $request->input('default_seats_on_vehicle');
        $record->men = $request->get('men');
        $record->women = $request->get('women');
        $record->kids = $request->get('kids');

        $record->owner = $owner;
        $owner_name = $request->get('owner_name');
        $record->owner_name = $owner === 'partner' ? $owner_name : '';

        $record->order = $request->get('order');
        $record->category_id = $request->get('category_id');
        $record->save();

        return redirect()
            ->route('admin.cars.cars.index')
            ->with('success', 'Машина была добавлена');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $car = Cars::where('id',$id)->with('category')->first();
        $categories = Category::all();

        return view('cars.edit',compact('car','id', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Lists\Car\UpdateRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(\App\Http\Requests\Lists\Car\UpdateRequest $request, int $id): RedirectResponse
    {
        $record = Cars::find($id);
        $record->name = $request->get('name');
        $record->car_number = $request->get('car_number');

        $owner = $request->get('owner');
        $record->passengers_amount = $owner === 'partner' ? 9999 : $request->input('default_seats_on_vehicle');
        $record->men = $request->get('men');
        $record->women = $request->get('women');
        $record->kids = $request->get('kids');

        $record->owner = $owner;
        $owner_name = $request->get('owner_name');
        $record->owner_name = $owner === 'partner' ? $owner_name : '';

        $record->order = $request->get('order');
        $record->category_id = $request->get('category_id');
        $record->save();

        return redirect()
            ->route('admin.cars.cars.index')
            ->with('success', 'Машина была обновлена');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     * @throws Exception
     */
    public function destroy(int $id): RedirectResponse
    {
        $record = Cars::find($id);
        $record->delete();

        return redirect()
            ->route('admin.cars.cars.index')
            ->with('success','Машина была удалена');
    }

    /**
     * Вывод таблицы категорий с их местами по умоланию
     */
    public function defaultSeats()
    {
        $categories = Category::all();

        return view('cars.defaultSeatsIndex', compact('categories'));
    }

    /**
     * Вывод таблицы категорий с их местами по умоланию
     * @param $id
     * @return array|Factory|View|mixed
     */
    public function defaultSeatsEdit($id)
    {
        $category = Category::find($id);

        return view('cars.defaultSeatsEdit', compact('category', 'id'));
    }

    /**
     * Вывод таблицы категорий с их местами по умоланию
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function defaultSeatsUpdate(UpdateRequest $request, $id)
    {
        $category = Category::find($id);
        $category->default_seats_on_vehicle = $request->input('default_seats_on_vehicle');
        $category->men = $request->input('men');
        $category->women = $request->input('women');
        $category->kids = $request->input('kids');
        $category->save();

        return redirect(route('admin.cars.defaultSeats.index'));
    }

    /**
     * @param $id
     * @param Request $request
     * @return void
     */
    public function setStatus($id, Request $request)
    {
        Cars::where('id',$id)->update(['active'=> $request->input('status')]);

        return response()->json([], Response::HTTP_ACCEPTED);
    }

}
