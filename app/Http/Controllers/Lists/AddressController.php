<?php

namespace App\Http\Controllers\Lists;

use App\Address;
use App\Http\Requests\Lists\Address\CreateRequest;
use App\Http\Controllers\Controller;
use \App\Http\Resources\Address as AddressResource;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('dispatcher')->only('index', 'create', 'store');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $addresses = Address::all();

        return view('lists.addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('lists.addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @return Response
     */
    public function store(CreateRequest $request)
    {
        $address = new Address();
        $address->name = $request->input('name');
        $address->save();

        return redirect(route('lists.addresses.index'))->with('success', 'Адрес добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Address $address
     * @return void
     */
    public function edit(Address $address)
    {
        return view('lists.addresses.edit', compact('address'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Address $address
     * @return void
     */
    public function update(CreateRequest $request, Address $address)
    {
        $address->name = $request->input('name');
        $address->save();

        return redirect(route('lists.addresses.index'))->with('success', 'Адрес изменен!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Address $address
     * @return void
     * @throws Exception
     */
    public function destroy(Address $address)
    {
        try {
            $address->delete();
        } catch (Exception $exception) {
        }

        return redirect(route('lists.addresses.index'))->with('success', 'Адрес удален!');
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function info()
    {
        $addresses = Address::all();

        return AddressResource::collection($addresses);
    }
}
