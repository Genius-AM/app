@extends('layouts.errors')
@section('title', '401')

@section('content')
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h1>Oops!</h1>
                <h2>401 - Unauthorized</h2>
            </div>
            <a href="{{ route('index') }}">На главную</a>
        </div>
    </div>
@endsection
