@extends('layouts.new__layout')

@section('title', 'Новая рассылка')

@section('content')
    <div class="container-fluid">
        @include('layouts.messages')

        <form id="order-form" method="POST" action="{{ route('lists.promotions.store') }}">
            @csrf
            <div class="row">
                <div class="col-md">
                    <div class="form-item">
                        <legend>Категория</legend>
                        <select id="category_id" class="nice-select" name="category_id" required autofocus>
                            <option value="">Не выбрана</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md hidden" id="subcategory">
                    <div class="form-item">
                        <legend>Подкатегория</legend>
                        <select id="subcategory_id" class="nice-select" name="subcategory_id">
                            <option value="">Не выбрана</option>
                        </select>
                    </div>
                </div>

                <div class="col-md">
                    <div class="form-item">
                        <legend>Количество</legend>
                        <input type="text" name="count" id="count" readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="form-item">
                        <legend>Текст</legend>
                        <input type="text" name="text" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="float-right">
                        <button type="submit" class="btn btn-green">Отправить</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            const categories = {!! $categories !!};

            const getCount = function () {
                $.ajax({
                    url: '{{ route('lists.promotions.count') }}',
                    type: "GET",
                    data: {
                        category: $('#category_id').val(),
                        subcategory: $('#subcategory_id').val(),
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $('#count').val(response);
                    }
                });
            }

            $('#category_id').change(e => {
                if (e.target.value) {
                    const category = categories.find(item => Number(item.id) === Number(e.target.value));

                    $('#subcategory_id').empty();

                    if (category.subcategories.length > 1) {
                        $('#subcategory').show();

                        $('#subcategory_id').append('<option value="">Не выбрана</option>')
                        for (const subcategory of category.subcategories) {
                            $('#subcategory_id').append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                        }
                    } else {
                        $('#subcategory').hide();
                    }

                    getCount();
                }
            });

            $('#subcategory_id').change(() => getCount());
        });
    </script>
@endsection
