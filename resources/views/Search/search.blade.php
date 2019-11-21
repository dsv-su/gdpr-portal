@extends('layouts.master')

@section('content')
        <div class="small">GDPR extract requested from GDPR officer</div>
        <div class="small">
        <table table-sm border border p-5>
            <tr><td>Personnr:</td><td>{{$personnr}}</td></tr>
            <tr><td>Email:</td><td>{{$gdpr_email}}</td></tr>
            <tr><td>UserId:</td><td>{{$gdpr_userid}}</td></tr>
        </table>
        </div>
        <br>
        <div class="row row-no-gutters">
            <div class="col border border-primary">
            Body

            </div>
        </div>

@endsection
