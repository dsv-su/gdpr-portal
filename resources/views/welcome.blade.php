@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Plugin Test 1 - leauge plugin</div>
            <div class="card-body">
                <p>Oauth2 token</p>
                <a href="/signin" class="btn btn-outline-primary" role="button">GET</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Plugin Test 2 - guzzle plugin</div>
            <div class="card-body">
                <p>Oauth2 token</p>
                <a href="/guzzle" class="btn btn-outline-primary" role="button">GET</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Plugin Test 3 - moodle plugin</div>
            <div class="card-body">
                <p>Get zip-file</p>
                <a href="/moodle" class="btn btn-outline-primary" role="button">GET</a>
            </div>
        </div>
    </div>

@endsection
