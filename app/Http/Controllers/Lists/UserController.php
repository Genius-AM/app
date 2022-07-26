<?php

namespace App\Http\Controllers\Lists;

use App\Category;
use App\Http\Controllers\Controller;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use \App\Http\Resources\User as UserResource;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param Role $role
     * @param Category $category
     * @return AnonymousResourceCollection
     */
    public function users(Request $request, Role $role, Category $category)
    {
        $users = User::where('role_id', $role->id)->where('category_id', $category->id)->get()->sortBy('name');

        return UserResource::collection($users);
    }

    /**
     * Получить список менеджеров
     *
     * @return AnonymousResourceCollection
     */
    public function managers()
    {
        $managers = User::manager()->get();

        return UserResource::collection($managers);
    }
}
