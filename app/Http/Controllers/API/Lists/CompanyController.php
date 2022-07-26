<?php

namespace App\Http\Controllers\API\Lists;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Companies\InfoRequest;
use \App\Http\Resources\Company as CompanyResource;
use App\Models\Company;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/Lists/Companies/All",
     *     summary="Получение компаний",
     *     description="Получение компаний",
     *     tags={"lists"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Company")
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
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $companies = Company::all();

        return CompanyResource::collection($companies);
    }

    /**
     * @SWG\Get(
     *     path="/Lists/Companies/Info",
     *     summary="Получение конкретной компании",
     *     description="Получение конкретной компании",
     *     tags={"lists"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="query",
     *         description="Company id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Company")
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
     * @param InfoRequest $request
     * @return CompanyResource
     */
    public function info(InfoRequest $request)
    {
        $company = Company::findOrFail($request->input('id'));

        return CompanyResource::make($company);
    }
}
