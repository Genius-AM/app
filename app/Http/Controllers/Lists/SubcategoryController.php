<?php

namespace App\Http\Controllers\Lists;

use App\Category;
use App\Http\Requests\Lists\Subcategory\StoreRequest;
use App\Http\Requests\Lists\Subcategory\UpdateRequest;
use App\Subcategory;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $subcategories = Subcategory::all();

        return view('lists.subcategories.index', compact('subcategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $categories = Category::all();

        return view('lists.subcategories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(StoreRequest $request)
    {
        $subcategory = new Subcategory();
        $subcategory->category_id = $request->input('category_id');
        $subcategory->name = $request->input('name');
        $subcategory->men = $request->input('men');
        $subcategory->women = $request->input('women');
        $subcategory->kids = $request->input('kids');
        $subcategory->save();

        return redirect(route('lists.subcategories.index'))->with('success', 'Подкатегория добавлена');
    }

    /**
     * Display the specified resource.
     *
     * @param Subcategory $subcategory
     * @return void
     */
    public function show(Subcategory $subcategory)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Subcategory $subcategory
     * @return Application|Factory|View
     */
    public function edit(Subcategory $subcategory)
    {
        return view('lists.subcategories.edit', compact('subcategory'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param Subcategory $subcategory
     * @return Application|RedirectResponse|Redirector
     */
    public function update(UpdateRequest $request, Subcategory $subcategory)
    {
        $subcategory->name = $request->input('name');
        $subcategory->men = $request->input('men');
        $subcategory->women = $request->input('women');
        $subcategory->kids = $request->input('kids');
        $subcategory->save();

        return redirect(route('lists.subcategories.index'))->with('success', 'Подкатегория изменена!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Subcategory $subcategory
     * @return Application|RedirectResponse|Redirector
     */
    public function destroy(Subcategory $subcategory)
    {
        try {
            $subcategory->delete();
        } catch (Exception $exception) {
        }

        return redirect(route('lists.subcategories.index'))->with('success', 'Подкатегория удалена!');
    }
}
