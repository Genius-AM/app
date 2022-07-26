<?php

namespace App\Http\Controllers\API\Lists;

use App\Http\Resources\AgeCategory as AgeCategoryResource;
use App\Models\AgeCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AgeCategoryController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/Lists/AgeCategories/All",
     *     summary="Получение категорий возраста",
     *     description="Получение категорий возраста",
     *     tags={"lists"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/AgeCategory")
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
        $ageCategories = AgeCategory::all();

        return AgeCategoryResource::collection($ageCategories);
    }
}
