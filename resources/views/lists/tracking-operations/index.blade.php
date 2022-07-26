@extends('layouts.new__layout')

@section('title', 'Отслеживание операций')

@section('content')
    <div class="container-fluid">
        <form action="{{ route('lists.tracking-operations.index') }}">
            <div class="row">
                <div class="col-2 mb-2">
                    <label for="user_id" class="m-0">Пользователь</label>
                    <select id="user_id" name="user_id" class="form-control form-control-sm">
                        <option value="">Выберите</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @if($user->id == request()->input('user_id')) selected @endif>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-2 mb-2">
                    <label for="ip" class="m-0">Ip</label>
                    <input type="text" id="ip" name="ip" class="form-control form-control-sm" value="{{ request()->input('ip') }}">
                </div>
                <div class="col-2 mb-2">
                    <label for="platform" class="m-0">Платформа</label>
                    <input type="text" id="platform" name="platform" class="form-control form-control-sm" value="{{ request()->input('platform') }}">
                </div>
                <div class="col-2 mb-2">
                    <label for="browser" class="m-0">Браузер</label>
                    <input type="text" id="browser" name="browser" class="form-control form-control-sm" value="{{ request()->input('browser') }}">
                </div>
                <div class="col-4 mb-2">
                    <label for="start_date" class="m-0">Период</label>
                    <div class="d-flex">
                        <input type="datetime-local" id="start_date" name="start_date" class="form-control form-control-sm" value="{{ request()->input('start_date') }}">
                        <input type="datetime-local" id="end_date" name="end_date" class="form-control form-control-sm" value="{{ request()->input('end_date') }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-5 mb-2">
                    <label for="user_agent" class="m-0">User-Agent</label>
                    <input type="text" id="user_agent" name="user_agent" class="form-control form-control-sm" value="{{ request()->input('user_agent') }}">
                </div>
                <div class="col-5 mb-2">
                    <label for="url" class="m-0">Url</label>
                    <input type="text" id="url" name="url" class="form-control form-control-sm" value="{{ request()->input('url') }}">
                </div>
                <div class="col-2 mb-2 btn-group-sm d-flex flex-column justify-content-end">
                    <button type="submit" class="btn btn-success btn-sm">Найти</button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-12">
                <div class="table-wrap">
                    <table class="edit-car-info">
                        <thead>
                            <tr>
                                <th class="col-1">Пользователь</th>
                                <th class="col-1">Ip</th>
                                <th class="col-4">User-Agent</th>
                                <th class="col-1">Платформа</th>
                                <th class="col-1">Браузер</th>
                                <th class="col-3">Url</th>
                                <th class="col-1">Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($trackingOperations))
                                @foreach($trackingOperations as $trackingOperation)
                                    <tr>
                                        <td>{{ $trackingOperation->user->name }}</td>
                                        <td>{{ $trackingOperation->ip }}</td>
                                        <td>{{ $trackingOperation->user_agent }}</td>
                                        <td>{{ $trackingOperation->platform }}</td>
                                        <td>{{ $trackingOperation->browser }} {{ $trackingOperation->browser_version }}</td>
                                        <td>{{ $trackingOperation->url }}</td>
                                        <td>{{ \Carbon\Carbon::parse($trackingOperation->created_at)->format('d.m.Y H:i:s') }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">Нет данных</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $trackingOperations->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
@endsection
