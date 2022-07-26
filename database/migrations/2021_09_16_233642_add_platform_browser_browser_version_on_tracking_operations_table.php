<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlatformBrowserBrowserVersionOnTrackingOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_operations', function (Blueprint $table) {
            $table->string('platform')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_operations', function (Blueprint $table) {
            $table->dropColumn('platform');
            $table->dropColumn('browser');
            $table->dropColumn('browser_version');
        });
    }
}
