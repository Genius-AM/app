<?php

namespace App\Console\Commands;

use App\Core\Timetable\TimetableOptions;
use App\Excursion;
use App\Order;
use App\Route;
use Carbon\Carbon;
use Illuminate\Console\Command;

class correctTimeInModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:time-in-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Time For Models';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orders = Order::all();

        foreach ($orders as $order) {
            $order->time = Carbon::createFromTimeString($order->time)->format('H:i:00');
            $order->save();
        }

        $excursions = Excursion::all();

        foreach ($excursions as $excursion) {
            $excursion->time = Carbon::createFromTimeString($excursion->time)->format('H:i:00');
            $excursion->save();
        }

        $this->info('times updated');
    }
}
