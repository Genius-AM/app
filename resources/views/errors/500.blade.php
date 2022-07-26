@extends('layouts.errors')
@section('title', '500')

@section('content')
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h1>Oops!</h1>
                <h2>500 - We're sorry but something went wrong</h2>
            </div>
            <a href="{{ route('index') }}">На главную</a>
        </div>
    </div>
@endsection