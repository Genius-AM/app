<?php

namespace App\Http\Controllers\Lists;

use App\Http\Requests\Lists\AgeCategory\CreateRequest;
use App\Http\Requests\Lists\AgeCategory\EditRequest;
use App\Models\AgeCategory;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AgeCategoryController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $ageCategories = AgeCategory::all();

        return view('lists.age-categories.index', compact('ageCategories'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('lists.age-categories.create');
    }

    /**
     * @param CreateRequest $request
     * @return RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        AgeCategory::create($request->only(['from', 'to']));

        return redirect()->route('lists.age-categories.index')
            ->with('success', 'Категория возраста добавлена');
    }

    /**
     * @param AgeCategory $ageCategory
     * @return Application|Factory|View
     */
    public function edit(AgeCategory $ageCategory)
    {
        return view('lists.age-categories.edit', compact('ageCategory'));
    }

    /**
     * @param EditRequest $request
     * @param AgeCategory $ageCategory
     * @return RedirectResponse
     */
    public function update(EditRequest $request, AgeCategory $ageCategory)
    {
        $ageCategory->update($request->only(['from', 'to']));

        return redirect()->route('lists.age-categories.index')
            ->with('success', 'Категория возраста изменена');
    }

    /**
     * @param AgeCategory $ageCategory
     * @return RedirectResponse
     */
    public function destroy(AgeCategory $ageCategory)
    {
        try {
            $ageCategory->delete();

            return redirect()->route('lists.age-categories.index')
                ->with('success', 'Категория возраста удалена');
        } catch (Exception $exception) {
            return redirect()->route('lists.age-categories.index')
                ->with('error', 'Ошибка при удалении');
        }
    }
}
