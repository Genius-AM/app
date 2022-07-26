<?php

namespace App\Http\Controllers;

use App\Http\Controllers\API\JwtTokenBlacklistController;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Company;
use App\User;
use App\Category;
use App\Role;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * @param null $id
     * @return array|Factory|View|mixed
     */
    public function pull($id = null)
    {
        if(!$id) $id = Auth::id();

        $user = User::findOrFail($id);
        $user->load('role');

        if($user->role_id == 3) $user->load('routes');

        return view('profile', ['user' => $user]);
    }

    /**
     * @return array|Factory|View|mixed
     */
    public function staff()
    {
        $users = User::whereIn('role_id', [1,3])->with('role')->simplePaginate(6);

        return view('staff', ['users' => $users]);
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return view('admin.user.index', [
            'users' => User::with('role')->get()
        ]);
    }

    /**
     * Список привязанных устройств
     *
     * @return View
     */
    public function devices(): View
    {
        return view('admin.user.devices', [
            'users' => User::where('device_id', '!=', '')
                ->whereNotNull('device_id')
                ->with('role')
                ->get()
        ]);
    }

    /**
     * @return View
     */
    public function new(): View
    {
        $roles      = Role::all();
        $categories = Category::all();
        $companies  = Company::whereDoesntHave('driver')->get();

        return view('admin.user.new', compact('roles', 'categories', 'companies'));
    }

    /**
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function create(CreateUserRequest $request): JsonResponse
    {
        User::create($request->validated());

        return response()->json([
            'message' => 'Пользователь добавлен'
        ], 200);
    }

    /**
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $user = User::with('cars')->findOrFail($id);
        $user->load('role');

        $roles      = Role::all();
        $categories = Category::all();
        $companies  = Company::whereDoesntHave('driver')->get();

        return view('admin.user.edit', compact('user', 'roles', 'categories', 'companies'));
    }

    /**
     * @param UpdateUserRequest $request
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request): RedirectResponse
    {
        $user = User::findOrFail($request->input('id'));
        $user->fill($request->validated())->save();

        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Exception
     */
    public function delete(Request $request): RedirectResponse
    {
        $user = User::findOrFail($request->input('user'));

        $user->update(['login' => null]);

        $user->delete();

        return redirect()->back();
    }

    /**
     * @param User $user
     * @return RedirectResponse
     * @throws Exception
     */
    public function device_delete(User $user): RedirectResponse
    {
        $user->device_id = null;
        $user->save();

        //удаление токена в бд
        JwtTokenBlacklistController::removeTokensByUser($user);

        return redirect()->back();
    }
}
