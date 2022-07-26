<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth2:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => '/V1', 'middleware' => ['json.headers:api']], function () {
    // Auth
    Route::group(['prefix' => '/Auth', 'namespace' => 'API'], function () {
        // Ниже роуты, к которым может обращаться пользователь без ограничений
        // Авторизация пользователя
        Route::post('Login', 'AuthController@login');

        // Обновление JWT Token
        Route::get('Refresh', 'AuthController@refresh');

        // Ниже роуты, доступные только для авторизованного пользователя
        Route::group(['middleware' => ['auth2:api']], function () {
            // Get user info
            Route::post('User', 'AuthController@user');
            // Logout user from application
            Route::post('Logout', 'AuthController@logout');
        });
    });

    // Ниже роуты, доступные только для авторизованного пользователя
    Route::group(['middleware' => ['auth2:api']], function () {
        // Device
        Route::group(['prefix' => '/Device', 'namespace' => 'API'], function () {
            //сохраняем токен устройства
            Route::post('/SaveDeviceToken', 'DeviceController@saveDeviceToken');
            //возврат версии приложения
            Route::get('/GetAppVersion', 'DeviceController@getAppVersion');
        });

        // API
        Route::group(['namespace' => 'API'], function () {
            // Manager
            Route::group(['prefix' => '/Manager', 'namespace' => 'Managers', 'middleware' => ['api.manager:api']], function () {
                //получаем актуальные заявки
                Route::post('/Order/GetActive', 'ManagerController@getActiveOrder');
                //получаем архивные заявки
                Route::post('/Order/GetArchive', 'ManagerController@getArchiveOrder');
                //добавление новой заявки
                Route::post('/Order/Add', 'ManagerController@storeNewOrder');
                //редактирование заявки
                Route::post('/Order/Edit', 'ManagerController@editOrder');
                //получить заявку
                Route::get('/Order/Get', 'ManagerController@getOrder');
                //Удаление заявки
                Route::post('/Order/Cancel', 'ManagerController@cancelOrder');

                //поулчить все доступные экскурсии по категории и их доступные времена
                Route::post('/Times/Get', 'ManagerController@getTimesByTypeAndDate');
                //получить категории
                Route::get('/Categories/Get', 'ManagerController@getCategories');
                //поулчить подкатегории по категории
                Route::get('/Subcategories/Get', 'ManagerController@getSubcategories');
                //поулчить маршруты по подкатегории
                Route::get('/Routes/Get', 'ManagerController@getRoutes');
                //поулчить свободные места
                Route::post('/FreePlaces/Get', 'ManagerController@getFreePlaces');
                Route::get('/TimesAndLimits', 'ManagerController@timesAndLimits');


                // Quadbike
                Route::group(['prefix' => '/Quadbike', 'namespace' => 'Quadbike'], function () {
                    Route::get('/TimesAndLimits', 'QuadbikeController@timesAndLimits');

                    // Order
                    Route::group(['prefix' => '/Order'], function () {
                        Route::post('/Add', 'QuadbikeController@storeNewOrder');
                        Route::post('/Edit', 'QuadbikeController@editOrder');
                    });
                });

                // Sea
                Route::group(['prefix' => '/Sea', 'namespace' => 'Sea'], function () {
                    Route::get('/TimesAndLimits', 'SeaController@timesAndLimits');

                    // Order
                    Route::group(['prefix' => '/Order'], function () {
                        Route::post('/Add', 'SeaController@storeNewOrder');
                        Route::post('/Edit', 'SeaController@editOrder');
                    });
                });
            });

            // Driver
            Route::group(['prefix' => '/Driver', 'namespace' => 'Drivers', 'middleware' => ['api.driver:api']], function () {
                //получаем актуальные рейсу
                Route::post('/Excursion/GetActive', 'DriverController@getActive');
                //получаем архивные рейсу
                Route::post('/Excursion/GetArchive', 'DriverController@getArchive');
                // отмена экскурсии
                Route::post('/Excursion/Cancel', 'DriverController@cancel');
                //получаем все данные по рейсу
                Route::get('/Excursion/Orders/Get', 'DriverController@getOrders');
                // Фиксация звонка по заявке
                Route::post('/Excursion/Orders/CallRecord', 'DriverController@orderCallRecord');
                //утверждение заявки
                Route::post('/Order/Agree', 'DriverController@setAgreeOrder');
                //отказ заявки
                Route::post('/Order/Cancel', 'DriverController@setCancelOrder');
                //отказ заявки после принятия
                Route::post('/Order/CancelAfterAccept', 'DriverController@setCancelAfterAcceptOrder');
                //возврат всей брони
                Route::post('/BookedTime/Get', 'DriverController@bookedTimesGet');
                //добавление или изменений брони
                Route::post('/BookTime/Set', 'DriverController@changeOrAddBookData');
                //добавление или изменений количества брони за определенный день и рейс
                Route::post('/PassengersAmount/Set', 'DriverController@changeOrAddPassengersAmountInExcursion');

                // Quadbike
                Route::group(['prefix' => '/Quadbike', 'namespace' => 'Quadbike'], function () {
                    Route::post('/PassengersAmount/Set', 'QuadbikeController@changeOrAddPassengersAmountInExcursion');
                });

                // Sea
                Route::group(['prefix' => '/Sea', 'namespace' => 'Sea'], function () {
                    Route::post('/PassengersAmount/Set', 'SeaController@changeOrAddPassengersAmountInExcursion');
                    Route::post('/BookTime/Set', 'SeaController@changeOrAddBookData');
                });
            });

            // Lists
            Route::group(['prefix' => '/Lists', 'namespace' => 'Lists'], function () {
                // Addresses
                Route::group(['prefix' => '/Addresses'], function () {
                    Route::get('/All', ['uses' => 'AddressController@index']);
                });
                // Company
                Route::group(['prefix' => '/Companies'], function () {
                    Route::get('/All', ['uses' => 'CompanyController@index']);
                    Route::get('/Info', ['uses' => 'CompanyController@info']);
                });
                // Routes
                Route::group(['prefix' => '/Routes'], function () {
                    Route::get('/All', ['uses' => 'RouteController@index']);
                });
                // AgeCategories
                Route::group(['prefix' => '/AgeCategories'], function () {
                    Route::get('/All', ['uses' => 'AgeCategoryController@index']);
                });
            });
        });
    });
});


//Route::middleware('json.headers:api')->prefix('V2')->group(function () {
//    Route::group(['prefix' => '/Auth'], function () {
//        // Ниже роуты, к которым может обращаться пользователь без ограничений
//        // Авторизация пользователя
//        Route::post('Login', 'API\AuthController@login');
//
//        // Обновление JWT Token
//        Route::get('Refresh', 'API\AuthController@refresh');
//
//        // Ниже роуты, доступные только для авторизованного пользователя
//        Route::middleware('auth2:api')->group(function () {
//            // Get user info
//            Route::post('User', 'API\AuthController@user');
//            // Logout user from application
//            Route::post('Logout', 'API\AuthController@logout');
//        });
//    });
//
//    Route::group(['namespace' => 'API', 'middleware' => ['auth2:api']], function () {
//        Route::group(['prefix' => '/Device'], function () {
//            //сохраняем токен устройства
//            Route::post('/SaveDeviceToken', 'DeviceController@saveDeviceToken');
//            //возврат версии приложения
//            Route::get('/GetAppVersion', 'DeviceController@getAppVersion');
//        });
//
//        Route::group(['prefix' => '/Manager', 'middleware' => ['manager:api'], 'namespace' => 'Managers'], function () {
//            //получаем актуальные заявки
//            Route::group(['prefix' => '/Order'], function () {
////                Route::post('/Active', 'OrderController@activeOrders');
//            });
//        });
//
//        // Справочники
//        Route::group(['prefix' => '/Lists', 'namespace' => 'Lists'], function () {
//            // Адреса
//            Route::group(['prefix' => '/Addresses'], function () {
//                Route::get('/All', ['uses' => 'AddressController@index']);
//            });
//            // Компании
//            Route::group(['prefix' => '/Companies'], function () {
//                Route::get('/All', ['uses' => 'CompanyController@index']);
//            });
//            // Маршруты
//            Route::group(['prefix' => '/Routes'], function () {
//                Route::get('/All', ['uses' => 'RouteController@index']);
//            });
//
//            // Категории
//            Route::group(['prefix' => '/Routes'], function () {
//                Route::get('/All', ['uses' => 'RouteController@index']);
//            });
//        });
//    });
//});