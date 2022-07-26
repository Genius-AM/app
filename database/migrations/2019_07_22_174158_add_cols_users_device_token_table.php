<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColsUsersDeviceTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_device_token', function (Blueprint $table) {
            $table->boolean('device_ios')
                ->default(0)
                ->after('token');
            $table->boolean('device_android')
                ->default(0)
                ->after('token');
        });

        //всем задаем значение, как буд-то андроид
        $devices = \App\UsersDeviceToken::all();
        if(count($devices) > 0){
            foreach($devices as $key => $value){
                $record = \App\UsersDeviceToken::find($value->id);
                $record->device_android = 1;
                $record->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_device_token', function (Blueprint $table) {
            $table->dropColumn('device_ios');
            $table->dropColumn('device_android');
        });
    }
}
