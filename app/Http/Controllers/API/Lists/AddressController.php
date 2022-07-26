<?php

namespace App\Http\Controllers\API\Lists;

use App\Address;
use App\Http\Controllers\Controller;
use \App\Http\Resources\Address as AddressResource;

class AddressController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/Lists/Addresses/All",
     *     summary="Получение адресов",
     *     description="Получение адресов",
     *     tags={"lists"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Address")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="405",
     *         description="User's token invalidate",
     *     ),
     * )
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $addresses = Address::all();

        return AddressResource::collection($addresses);
    }
}
