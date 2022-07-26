<?php

namespace App\Http\Controllers;

use App\Category;
use App\Subcategory;
use Illuminate\Http\JsonResponse;

class GelenRouteController extends Controller {

    /**
     * @param Category $category
     * @return JsonResponse
     */
	public function index(Category $category) : JsonResponse
    {
		return response()->json($category->routes);
	}

    /**
     * @return JsonResponse
     */
	public function categories() : JsonResponse
    {
        $categories = Category::activeCategories()->get();

        return response()->json($categories);
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
	public function subcategories(Category $category) : JsonResponse
    {
        $subcategories = Subcategory::where('category_id', $category->id)->get();

        return response()->json($subcategories);
    }

    /**
     * @param Category $category
     * @return JsonResponse
     */
    public function reports(Category $category) : JsonResponse
    {
        $reports = trans('reports.' . $category->id);

        if (is_array($reports)) {
            return response()->json($reports);
        }

        return response()->json([], 200);
    }
}
