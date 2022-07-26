  // function send(excursion) {
  //                   $.ajax({
  //                       url: '/dispatcher/excursion/send',
  //                       type: "POST",
  //                       data: {excursion: excursion},
  //                       headers: {
  //                           'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
  //                       },
  //                       success: function(){
  //                           $('#excursion'+excursion).remove()
  //                       }
  //                   });
  //               }


                // function associate(order) {
                //     $.ajax({
                //         url: '/dispatcher/change/order/associate',
                //         type: "POST",
                //         data: {id: order},
                //         headers: {
                //             'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                //         },
                //         success: function (response) {
                //             $('#driver_select').find('option').remove();
                //             $('#excursionnewform').find('.order_field').remove();
                //             $('#excursion_select').find('option').remove();
                //             $('#driver_select').append($('<option>', {
                //                 value: '',
                //                 text: 'Не выбран'
                //             }));
                //             $('#excursion_select').append($('<option>', {
                //                 value: '',
                //                 text: 'Не выбрана'
                //             }));
                //             // console.log(response.drivers);
                //             $.each(response.drivers, function(index, value){
                //                 $('#driver_select').append($('<option>', {
                //                     value: value.id,
                //                     text: value.name
                //                 }));
                //             });
                //             $('#driver_select').niceSelect('update');
                //
                //             $.each(response.excursions, function(index, value){
                //                 $('#excursion_select').append($('<option>', {
                //                     value: value.id,
                //                     text: value.id
                //                 }));
                //             });
                //             $('#excursion_select').niceSelect('update');
                //             let people = response.order.men + response.order.women + response.order.kids;
                //             $('#excursionnewform').append($('<input>', {
                //                 name: 'order',
                //                 class: 'order_field',
                //                 type: 'hidden',
                //                 value: response.order.id,
                //             }));
                //             $('#excursionnewform').append($('<input>', {
                //                 name: 'subcategory',
                //                 class: 'order_field',
                //                 type: 'hidden',
                //                 value: response.order.subcategory_id,
                //             }));
                //             $('#excursionnewform').append($('<input>', {
                //                 name: 'route',
                //                 class: 'order_field',
                //                 type: 'hidden',
                //                 value: response.order.route_id,
                //             }));
                //             $('#excursionnewform').append($('<input>', {
                //                 name: 'date',
                //                 class: 'order_field',
                //                 type: 'hidden',
                //                 value: response.order.date,
                //             }));
                //             $('#excursionnewform').append($('<input>', {
                //                 name: 'time',
                //                 class: 'order_field',
                //                 type: 'hidden',
                //                 value: response.order.time,
                //             }));
                //             $('#excursionnewform').append($('<input>', {
                //                 name: 'people',
                //                 class: 'order_field',
                //                 type: 'hidden',
                //                 value: people,
                //             }));
                //         }
                //     });
                // }
                $(document).ready(function() {
                    $('#driver_select').niceSelect();
                    $('#excursion_select').niceSelect();
                    $('.filters').change(function () {
                        $.ajax({
                            // url: '{{ route('dispatcher.orders.getShort', ['category' => $category, 'subcategory' => $subcategory]) }}',
                            url: '/dispatcher/change/orders/' + $category + '/' + $subcategory + '/',
                            type: "GET",
                            data: {route: $('#filter_route').val(),
                                date: $('#filter_date').val(),
                                manager: $('#filter_manager').val(),
                                time: $('#filter_time').val()},
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                $('.div2').html(response);
                            }
                        });
                    });


                    function vivod(v1)
                    {
                        var key =  localStorage.getItem('idsapis');
                      if(key)
                      {
                          if(key ==v1)
                          {
                          }
                          else
                          {
                              $(".btn-primary").click();
                              localStorage.setItem('idsapis', v1)
                          }

                      }
                      else
                      {
                          localStorage.setItem('idsapis', v1)
                      }
                    }

                     // function funcn(){
                     //    $.ajax({
                     //        // url: '{{ route('dispatcher.order.norder') }}',
                     //        url: '/dispatcher/order/norder',
                     //        type: "GET",
                     //        data: {dat: 1},
                     //        headers: {
                     //            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                     //        },
                     //        success: function (response) {
                     //            // return response;
                     //            console.log("=======" + response);
                     //            vivod(response);
                     //        }
                     //    });
                     // };

                    window.setInterval(function(){
                        // console.log("111111111111111111111111");
                        funcn();
                    }, 10000);
                });


                function sendclose(excursion) {

        // $.ajax({
        //     // url: '<?php echo e(route('dispatcher.excursion.sendclose')); ?>',
        //     url: '/dispatcher/excursion/sendclose',
        //     type: "POST",
        //     data: {id: excursion},
        //
        //     headers: {
        //         'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        //     },
        //     success: function(response){
        //         location.reload();
        //         //console.log(response);
        //
        //     }
        // });
    }

           // function associate2(order) {
           //     console.log(order);
           //     $("#driver_id").val(order)
           //     $( ".msgbotton" ).click(function() {
           //     //     $('.msgbotton').change(function () {
           //             console.log("++++");
           //         $.ajax({
           //            // url: '{{ route('dispatcher.excursion.create2') }}',
           //            url: '/dispatcher/excursion/create2',
           //            type: "POST",
           //             data: {id: order},
           //
           //             headers: {
           //                 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
           //             },
           //             success: function (response) {
           //                 // if (response) {
           //                 console.log(response);
           //                 // }
           //             }
           //         })
           //
           //     });
           //
           // }