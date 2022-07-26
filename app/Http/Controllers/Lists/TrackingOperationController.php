<?php

namespace App\Http\Controllers\Lists;

use App\Models\TrackingOperation;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TrackingOperationController extends Controller
{
    public function index(Request $request)
    {
        $trackingOperations = TrackingOperation::query()
            ->when($request->input('user_id'), function (Builder $query, $value) {
                $query->where('user_id', '=', $value);
            })
            ->when($request->input('ip'), function (Builder $query, $value) {
                $query->where('ip', 'like', '%' . $value . '%');
            })
            ->when($request->input('platform'), function (Builder $query, $value) {
                $query->where('platform', 'like', '%' . $value . '%');
            })
            ->when($request->input('browser'), function (Builder $query, $value) {
                $query->where(DB::raw('CONCAT(browser, \' \', browser_version)'), 'like', '%' . $value . '%');
            })
            ->when($request->input('user_agent'), function (Builder $query, $value) {
                $query->where('user_agent', 'like', '%' . $value . '%');
            })
            ->when($request->input('url'), function (Builder $query, $value) {
                $query->where('url', 'like', '%' . $value . '%');
            })
            ->when($request->input('start_date'), function (Builder $query, $value) {
                $query->where('created_at', '>=', Carbon::parse($value));
            })
            ->when($request->input('end_date'), function (Builder $query, $value) {
                $query->where('created_at', '<=', Carbon::parse($value));
            })
            ->whereHas('user', function (Builder $query) {
                $query->where('role_id', '!=', 4);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        $users = User::where('role_id', '!=', 4)->get();

        return view('lists.tracking-operations.index', compact('trackingOperations', 'users'));
    }
}
