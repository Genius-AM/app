<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

// The route group that will pass every request to a Middleware
Route::group(['middleware' => ['clearcache', 'tracking-operations']], function() {
    Auth::routes();

    Route::group(['namespace' => 'Auth'], function () {
        Route::get('/auth', ['as' => 'login', 'uses' => 'PageController@auth']);
        Route::post('/auth', ['uses' => 'LoginController@login']);
        Route::get('/logout', ['as' => 'logout', 'uses' => 'PageController@logout']);
    });

    Route::get('/profile/{id?}', ['as' => 'profile.pull', 'uses' => 'UserController@pull']);

    Route::group(['middleware' => ['auth']], function () {
        Route::get('/', ['as' => 'index', 'uses' => 'MainController@index']);

        // dispatcher
        Route::group(['as' => 'dispatcher.', 'prefix' => '/dispatcher', 'middleware' => ['dispatcher']], function () {
            //get all orders for dispatcher
            Route::get('/all-orders/{main_category}', ['as' => 'all-orders', 'uses' => 'GelenDispatcherController@getAllOrdersForTheCategory']);
            Route::post('cancel/order/{order}/{excursion}', ['as' => 'cancel.order', 'uses' => 'GelenDispatcherController@cancelOrder']);
            //get all managers with their orders
            Route::post('/managers', ['as' => 'managers', 'uses' => 'GelenManagerController@index']);
            //get all managers without their orders
            Route::get('/managers/all', ['as' => 'managers.all', 'uses' => 'GelenManagerController@getAll']);
            //get routes
            Route::get('/routes/{category}', ['as' => 'routes', 'uses' => 'GelenRouteController@index']);
            //get categories
            Route::get('/categories', ['as' => 'categories', 'uses' => 'GelenRouteController@categories']);
            //get subcategories
            Route::get('/{category}/subcategories', ['as' => 'subcategories', 'uses' => 'GelenRouteController@subcategories']);
            //get reports
            Route::get('/{category}/reports', ['as' => 'reports', 'uses' => 'GelenRouteController@reports']);
            //get all drivers
            Route::get('drivers', ['as' => 'drivers', 'uses' => 'GelenDriverController@index']);
            //get all cars
            Route::post('cars', ['as' => 'cars', 'uses' => 'GelenCarController@getCarsByDate']);
            //get all partners cars
            Route::post('partner_cars', ['as' => 'partner_cars', 'uses' => 'GelenCarController@getPartnerCarsByDate']);
            //get times
            Route::post('timesByDate', ['as' => 'timesByDate', 'uses' => 'TimesController@getTimesByDate']);
            //get book data
            Route::post('booked_time', ['as' => 'booked_time', 'uses' => 'GelenBookController@getBookData']);
            Route::post('booked_time_change', ['as' => 'booked_time_change', 'uses' => 'GelenBookController@changeBookData']);
            Route::post('booked_time_amount', ['as' => 'booked_time_amount', 'uses' => 'GelenBookController@amountBookData']);
            //get book data car
            Route::post('booked_time_car', ['as' => 'booked_time_car', 'uses' => 'GelenBookController@getBookDataCar']);
            Route::post('booked_time_car_change', ['as' => 'booked_time_car_change', 'uses' => 'GelenBookController@changeBookDataCar']);
            //assign order to a car
            Route::post('assign-order-to-car', ['as' => 'assign-order-to-car', 'uses' => 'GelenExcurssionController@new_store_again']);
            Route::post('assign-order-to-partner-car', ['as' => 'assign-order-to-partner-car', 'uses' => 'GelenExcurssionController@new_store_partner_again']);

            // excursion
            Route::group(['as' => 'excursion.', 'prefix' => '/excursion'], function () {
                Route::post('/send', ['as' => 'send', 'uses' => 'ExcursionController@send']);
                Route::post('/book', ['as' => 'book', 'uses' => 'ExcursionController@book']);
            });

            Route::get('/staff', ['as' => 'staff', 'uses' => 'UserController@staff']);

            Route::group(['namespace' => 'Order'], function () {
                // order
                Route::group(['as' => 'order.', 'prefix' => '/order'], function () {
                    Route::post('/info/{order}', ['as' => 'info', 'uses' => 'OrderController@orderInfo']);
                    Route::post('/change/{order}', ['as' => 'change', 'uses' => 'OrderController@orderChange']);
                    Route::post('/cancel/{order}', ['as' => 'cancel', 'uses' => 'OrderController@orderCancel']);
                    Route::post('/push-notification/{order}', ['as' => 'push', 'uses' => 'OrderController@pushNotificationToDriver']);
                });

                // canceled-orders
                Route::group(['as' => 'canceled-orders.', 'prefix' => '/canceled-orders'], function () {
                    Route::get('/', ['as' => 'index', 'uses' => 'CanceledOrderController@index']);
                    Route::post('/get-orders', ['as' => 'get-orders', 'uses' => 'CanceledOrderController@getOrders']);
                    Route::post('/general-excel', ['as' => 'general-excel', 'uses' => 'CanceledOrderController@generalExcel']);
                });
            });
        });

        // route
        Route::group(['namespace' => 'Route'], function () {
            Route::group(['as' => 'route.', 'prefix' => '/route'], function () {
                // times
                Route::group(['as' => 'times.', 'prefix' => '/times'], function () {
                    Route::get('/', ['as' => 'index', 'uses' => 'RouteController@index2']);
                    Route::post('/set', ['as' => 'set', 'uses' => 'RouteController@timesSet']);
                });
            });

            Route::get('/new/route/cars', ['as' => 'route.cars', 'uses' => 'RouteController@getCars']);
            Route::get('/new/route/subcategories', ['as' => 'route.subcategories', 'uses' => 'RouteController@getSubcategories']);
            Route::get('/routes', ['as' => 'routes', 'uses' => 'RouteController@getRoutes']);
            Route::get('/routeTimes', ['as' => 'routeTimes', 'uses' => 'RouteController@getRouteTimes']);
        });

        // admin
        Route::group(['as' => 'admin.', 'prefix' => '/admin', 'middleware' => ['admin']], function () {
            // App version
            Route::group(['as' => 'app_version.', 'prefix' => '/app_version'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'AppVersionController@index']);
                Route::post('/edit', ['as' => 'edit', 'uses' => 'AppVersionController@edit']);
                Route::post('/update', ['as' => 'update', 'uses' => 'AppVersionController@update']);
            });

            // user
            Route::group(['as' => 'user.', 'prefix' => '/user'], function () {
                Route::get('/new', ['as' => 'new', 'uses' => 'UserController@new']);
                Route::post('/create', ['as' => 'create', 'uses' => 'UserController@create']);
                Route::get('/show/{id}', ['as' => 'show', 'uses' => 'UserController@show']);
                Route::post('/delete', ['as' => 'delete', 'uses' => 'UserController@delete']);
                Route::post('/edit', ['as' => 'update', 'uses' => 'UserController@update']);
                Route::post('/device_delete/{user}', ['as' => 'device_delete', 'uses' => 'UserController@device_delete']);
            });

            // users
            Route::group(['as' => 'users.', 'prefix' => '/users'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'UserController@index']);
                Route::get('/devices', ['as' => 'devices', 'uses' => 'UserController@devices']);
            });

            // route
            Route::group(['namespace' => 'Route'], function () {
                Route::group(['as' => 'route.', 'prefix' => '/route'], function () {
                    // times
                    Route::group(['as' => 'times.', 'prefix' => '/times'], function () {
                        Route::get('/', ['as' => 'index', 'uses' => 'RouteController@index']);
                        Route::post('/set', ['as' => 'set', 'uses' => 'RouteController@timesSet']);
                    });

                    Route::group(['as' => 'cars.', 'prefix' => '/cars'], function () {
                        Route::get('{route}/', ['as' => 'index', 'uses' => 'RouteCarController@index']);
                        Route::get('{route}/add/{route_car?}', ['as' => 'add', 'uses' => 'RouteCarController@add']);
                        Route::post('{route}/create/{route_car?}', ['as' => 'create', 'uses' => 'RouteCarController@create']);
                    });

                    Route::get('/edit/{id}', ['as' => 'show', 'uses' => 'RouteController@show']);
                    Route::post('/delete', ['as' => 'delete', 'uses' => 'RouteController@delete']);
                    Route::post('/edit', ['as' => 'update', 'uses' => 'RouteController@update']);
                });

                Route::get('/new/route/cars', ['as' => 'route.cars', 'uses' => 'RouteController@getCars']);
                Route::get('/new/route/subcategories', ['as' => 'route.subcategories', 'uses' => 'RouteController@getSubcategories']);
                Route::get('/new/route', ['as' => 'route.new', 'uses' => 'RouteController@new']);
                Route::post('/new/route', ['as' => 'route.create', 'uses' => 'RouteController@create']);
                Route::get('/routes', ['as' => 'routes', 'uses' => 'RouteController@getRoutes']);
                Route::get('/routeTimes', ['as' => 'routeTimes', 'uses' => 'RouteController@getRouteTimes']);
                Route::get('/routes/all', ['as' => 'routes.list', 'uses' => 'RouteController@get']);
            });

            // cars
            Route::group(['as' => 'cars.', 'prefix' => '/cars', 'namespace' => 'Cars'], function () {
                // defaultSeats
                Route::group(['as' => 'defaultSeats.', 'prefix' => '/defaultSeats'], function () {
                    Route::get('/', ['as' => 'index', 'uses' => 'CarsController@defaultSeats']);
                    Route::get('/{id}', ['as' => 'edit', 'uses' => 'CarsController@defaultSeatsEdit']);
                    Route::patch('/{update}', ['as' => 'update', 'uses' => 'CarsController@defaultSeatsUpdate']);
                });

                // attach
                Route::get('/{id}/attach', ['as' => 'attach.index', 'uses' => 'CarsAttachmentHistoryController@attach_page']);
                Route::patch('/{id}/attach', ['as' => 'attach.attach', 'uses' => 'CarsAttachmentHistoryController@attach']);
                Route::get('/{id}/attach_list', ['as' => 'attach.list', 'uses' => 'CarsAttachmentHistoryController@attach_list']);

                Route::post('cars/{id}/status',['as' => 'cars.status', 'uses' => 'CarsController@setStatus']);
                Route::resource('cars', 'CarsController');

                // timetables
                Route::group(['as' => 'timetables.', 'prefix' => '/timetables', 'namespace' => 'Timetable'], function () {
                    Route::get('/{route_car}', ['as' => 'index', 'uses' => 'TimetableController@index']);
                    Route::get('/actual/{route_car}', ['as' => 'actual', 'uses' => 'TimetableController@actualTimetable']);
                    Route::get('/check/{excursion_car_timetable}', ['as' => 'check', 'uses' => 'TimetableController@check']);
                    Route::post('/save/{route_car}', ['as' => 'check', 'uses' => 'TimetableController@save']);
                });
            });

        });

        Route::group(['as' => 'lists.', 'prefix' => '/lists', 'namespace' => 'Lists'], function () {
            // Получить список менеджеров
            Route::get('/users/managers', ['as' => 'managers', 'uses' => 'UserController@managers']);

            Route::get('/users/{role}/{category}', ['as' => 'users', 'uses' => 'UserController@users']);
            Route::get('/pointes/info', ['as' => 'pointes.info', 'uses' => 'AddressController@info']);
            Route::resource('addresses', 'AddressController');
            Route::resource('companies', 'CompanyController');
            Route::resource('subcategories', 'SubcategoryController')->middleware('admin');
            Route::resource('age-categories', 'AgeCategoryController');


            Route::get('promotions/count', ['as' => 'promotions.count', 'uses' => 'PromotionController@getCount'])->middleware('dispatcher');;
            Route::resource('promotions', 'PromotionController')->only('index', 'create', 'store')->middleware('dispatcher');;

            Route::get('/tracking-operations', ['as' => 'tracking-operations.index', 'uses' => 'TrackingOperationController@index']);
        });

        Route::group(['as' => 'reports.', 'prefix' => '/reports', 'namespace' => 'Reports'], function () {
            Route::group(['as' => 'actual-recording.', 'prefix' => '/actual-recording'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'ActualRecordingController@index']);
                Route::post('/data', ['as' => 'data', 'uses' => 'ActualRecordingController@data']);
                Route::post('/excel', ['as' => 'excel', 'uses' => 'ActualRecordingController@excel']);
            });
            Route::group(['as' => 'age-category.', 'prefix' => '/age-category'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'AgeCategoryController@index']);
                Route::post('/data', ['as' => 'data', 'uses' => 'AgeCategoryController@data']);
                Route::post('/excel', ['as' => 'excel', 'uses' => 'AgeCategoryController@excel']);
            });
            Route::group(['as' => 'deleted-order.', 'prefix' => '/deleted-order'], function () {
                Route::get('/', ['as' => 'index', 'uses' => 'DeletedOrderController@index']);
                Route::post('/data', ['as' => 'data', 'uses' => 'DeletedOrderController@data']);
                Route::post('/excel', ['as' => 'excel', 'uses' => 'DeletedOrderController@excel']);
            });

            Route::get('/show/{any?}', ['as' => 'index', 'uses' => 'MainReportController@index'])->where('any', '.*');

            Route::group(['as' => 'djipping.', 'prefix' => '/djipping', 'namespace' => 'Djipping'], function () {
                Route::group(['as' => 'driver.', 'prefix' => '/driver', 'namespace' => 'Driver'], function () {
                    Route::get('/general', ['as' => 'general', 'uses' => 'ReportController@general']);
                    Route::post('/general-excel', ['as' => 'general-excel', 'uses' => 'ReportController@generalExcel']);
                    Route::get('/route', ['as' => 'route', 'uses' => 'ReportController@route']);
                    Route::post('/route-excel', ['as' => 'route-excel', 'uses' => 'ReportController@routeExcel']);
                });

                Route::group(['as' => 'manager.', 'prefix' => '/manager', 'namespace' => 'Manager'], function () {
                    Route::get('/general', ['as' => 'general', 'uses' => 'ReportController@general']);
                    Route::get('/general2', ['as' => 'general2', 'uses' => 'ReportController@general2']);
                    Route::post('/general-excel', ['as' => 'general-excel', 'uses' => 'ReportController@generalExcel']);
                    Route::get('/address', ['as' => 'address', 'uses' => 'ReportController@address']);
                    Route::post('/address-excel', ['as' => 'address-excel', 'uses' => 'ReportController@addressExcel']);
                    Route::get('/route-time', ['as' => 'route-time', 'uses' => 'ReportController@routeTime']);
                    Route::get('/time-for-route-time', ['as' => 'time-for-route-time', 'uses' => 'ReportController@getTimesForRouteTime']);
                    Route::post('/route-time-excel', ['as' => 'route-time-excel', 'uses' => 'ReportController@routeTimeExcel']);
                    Route::get('/deleted-orders', ['as' => 'deleted-orders', 'uses' => 'ReportController@getDeletedOrders']);
                    Route::post('/deleted-orders-excel', ['as' => 'deleted-orders-excel', 'uses' => 'ReportController@deletedOrdersExcel']);
                    Route::get('/orders', ['as' => 'orders', 'uses' => 'ReportController@getOrders']);
                    Route::post('/orders-excel', ['as' => 'orders-excel', 'uses' => 'ReportController@ordersExcel']);
                });
            });

            Route::group(['as' => 'quadro.', 'prefix' => '/quadro', 'namespace' => 'Quadro'], function () {
                Route::group(['as' => 'driver.', 'prefix' => '/driver', 'namespace' => 'Driver'], function () {
                    Route::get('/general', ['as' => 'general', 'uses' => 'ReportController@general']);
                    Route::post('/general-excel', ['as' => 'general-excel', 'uses' => 'ReportController@generalExcel']);
                });

                Route::group(['as' => 'manager.', 'prefix' => '/manager', 'namespace' => 'Manager'], function () {
                    Route::get('/general', ['as' => 'general', 'uses' => 'ReportController@general']);
                    Route::post('/general-excel', ['as' => 'general-excel', 'uses' => 'ReportController@generalExcel']);
                    Route::get('/deleted-orders', ['as' => 'deleted-orders', 'uses' => 'ReportController@getDeletedOrders']);
                    Route::post('/deleted-orders-excel', ['as' => 'deleted-orders-excel', 'uses' => 'ReportController@deletedOrdersExcel']);
                    Route::get('/orders', ['as' => 'orders', 'uses' => 'ReportController@getOrders']);
                    Route::post('/orders-excel', ['as' => 'orders-excel', 'uses' => 'ReportController@ordersExcel']);
                });
            });

            Route::group(['as' => 'diving.', 'prefix' => '/diving', 'namespace' => 'Diving'], function () {
                Route::group(['as' => 'manager.', 'prefix' => '/manager', 'namespace' => 'Manager'], function () {
                    Route::get('/general', ['as' => 'general', 'uses' => 'ReportController@general']);
                    Route::post('/general-excel', ['as' => 'general-excel', 'uses' => 'ReportController@generalExcel']);
                    Route::get('/deleted-orders', ['as' => 'deleted-orders', 'uses' => 'ReportController@getDeletedOrders']);
                    Route::post('/deleted-orders-excel', ['as' => 'deleted-orders-excel', 'uses' => 'ReportController@deletedOrdersExcel']);
                    Route::get('/orders', ['as' => 'orders', 'uses' => 'ReportController@getOrders']);
                    Route::post('/orders-excel', ['as' => 'orders-excel', 'uses' => 'ReportController@ordersExcel']);
                });
            });

            Route::group(['as' => 'sea.', 'prefix' => '/sea', 'namespace' => 'Sea'], function () {
                Route::group(['as' => 'manager.', 'prefix' => '/manager', 'namespace' => 'Manager'], function () {
                    Route::get('/general', ['as' => 'general', 'uses' => 'ReportController@general']);
                    Route::post('/general-excel', ['as' => 'general-excel', 'uses' => 'ReportController@generalExcel']);
                    Route::get('/deleted-orders', ['as' => 'deleted-orders', 'uses' => 'ReportController@getDeletedOrders']);
                    Route::post('/deleted-orders-excel', ['as' => 'deleted-orders-excel', 'uses' => 'ReportController@deletedOrdersExcel']);
                    Route::get('/orders', ['as' => 'orders', 'uses' => 'ReportController@getOrders']);
                    Route::post('/orders-excel', ['as' => 'orders-excel', 'uses' => 'ReportController@ordersExcel']);
                    Route::get('/adults-children', ['as' => 'adults-children', 'uses' => 'ReportController@getAdultsChildren']);
                    Route::post('/adults-children-excel', ['as' => 'adults-children-excel', 'uses' => 'ReportController@adultsChildrenExcel']);
                });
            });
        });
    });
});
