<?php

namespace App\Http\Controllers\Lists;

use App\Category;
use App\Http\Requests\Lists\Promotion\CreateRequest;
use App\Jobs\PromotionJob;
use App\Models\Promotion;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $promotions = Promotion::all();

        return view('lists.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $categories = Category::with('subcategories')->get();

        return view('lists.promotions.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateRequest $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(CreateRequest $request)
    {
        $promotion = new Promotion();
        $promotion->category_id = $request->input('category_id');
        $promotion->subcategory_id = $request->input('subcategory_id');
        $promotion->text = $request->input('text');
        $promotion->count = 0;
        $promotion->save();

        PromotionJob::dispatch(
            $promotion,
            (int)$request->input('category_id'),
            (int)$request->input('subcategory_id'),
            $request->input('text')
        );

        return redirect(route('lists.promotions.index'))->with('success', 'Рассылка отправлена');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCount(Request $request): JsonResponse
    {
        return response()->json(count($this->getPhones($request->input('category'), $request->input('subcategory'))));
    }

    /**
     * @param int $category
     * @param int|null $subcategory
     * @return array
     */
    private function getPhones(int $category, ?int $subcategory = null): array
    {
        $phones = [];

        $orders = Order::where('category_id', '=', $category)
            ->when($subcategory, function (Builder $query, $subcategory) {
                $query->where('subcategory_id', '=', $subcategory);
            })
            ->get();
        foreach ($orders as $order) {
            $this->addPhone($phones, [$order->client->phone, $order->client->phone_2]);
        }

        return array_unique($phones);
    }

    /**
     * @param array $phones
     * @param array $newPhones
     */
    private function addPhone(array &$phones, array $newPhones)
    {
        foreach ($newPhones as $newPhone) {
            $phone = preg_replace('/\D/', '', $newPhone);
            if ($phone && strlen($phone) == 11) {
                $phones[] = "+7" . substr($phone, 1);
            }
        }
    }
}
