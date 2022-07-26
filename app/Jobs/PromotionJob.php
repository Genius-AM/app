<?php

namespace App\Jobs;

use App\Core\Notifications\SmscApi;
use App\Models\Promotion;
use App\Order;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PromotionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $promotion;

    protected $category;

    protected $subcategory;

    protected $text;

    /**
     * Create a new job instance.
     *
     * @param Promotion $promotion
     * @param int $category
     * @param int|null $subcategory
     * @param string $text
     */
    public function __construct(Promotion $promotion, int $category, ?int $subcategory, string $text)
    {
        $this->promotion = $promotion;
        $this->category = $category;
        $this->subcategory = $subcategory;
        $this->text = $text;
    }

    /**
     * Execute the job.
     *
     * @throws GuzzleException
     */
    public function handle()
    {
        $phones = $this->getPhones($this->category);

        foreach ($phones as $phone) {
            SmscApi::create()->phone($phone)->send($this->text);

            $this->promotion->increment('count');
        }
    }

    /**
     * @param int $category
     * @return array
     */
    private function getPhones(int $category): array
    {
        $phones = [];

        $orders = Order::where('category_id', '=', $category)
            ->when($this->subcategory, function (Builder $query, $subcategory) {
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
