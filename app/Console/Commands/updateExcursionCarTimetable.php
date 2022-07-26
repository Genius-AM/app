<?php

namespace App\Console\Commands;

use App\Core\Timetable\TimetableOptions;
use App\Models\ExcursionCarTimetable;
use App\Route;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class updateExcursionCarTimetable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:excursion-car-timetable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed ExcursionCarTimetable';

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
        $routes = Route::all();
        foreach ($routes as $route) {
            foreach ($route->cars as $car) {
                TimetableOptions::add($route, $car);
            }
        }

        $this->info('ExcursionCarTimetable seeded');
    }
}
