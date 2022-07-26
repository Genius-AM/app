<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CategoriesTableSeeder::class,
            SubcategoriesTableSeeder::class,
            UsersTableSeeder::class,
            StatusesTableSeeder::class,
            RolesTableSeeder::class,
            TimesTableSeeder::class,
            TimesSecondTableSeeder::class,
            BusstopsTableSeeder::class,
            RouteUserTableSeeder::class,
            StatusesNewTableSeeder::class,
            StatusesAddNewTableSeeder::class,
            AddNewRecordRoutesTableSeeder::class,
        ]);
    }
}
