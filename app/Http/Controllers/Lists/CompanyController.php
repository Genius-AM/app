<?php

namespace App\Http\Controllers\Lists;

use App\Models\Company;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $companies = Company::all();

        return view('lists.companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('lists.companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $company = new Company();
        $company->name = $request->input('name');
        $company->save();

        return redirect(route('lists.companies.index'))->with('success', 'Компания добавлена');
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
     * @param Company $company
     * @return Response
     */
    public function edit(Company $company)
    {
        return view('lists.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Company $company
     * @return Response
     */
    public function update(Request $request, Company $company)
    {
        $company->name = $request->input('name');
        $company->save();

        return redirect(route('lists.companies.index'))->with('success', 'Компания изменена!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Company $company
     * @return void
     */
    public function destroy(Company $company)
    {
        try {
            $company->delete();
        } catch (Exception $exception) {
        }

        return redirect(route('lists.companies.index'))->with('success', 'Компания удалена!');
    }
}
