@extends('layouts.errors')
@section('title', '419')

@section('content')
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h1>Oops!</h1>
                <h2>419 - The Page can't be found</h2>
            </div>
            <a href="{{ route('index') }}">На главную</a>
        </div>
    </div>
@endsection

@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
