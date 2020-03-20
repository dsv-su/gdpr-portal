@extends('layouts.master_swe')

@section('content')
    <div class="container">

    <h2>GDPR Ladda upp utdrag</h2>
        <form method="POST" action="{{ route('store') }}" enctype="multipart/form-data">
         @csrf
         <div class="form-group">
             <input type="hidden" name="case" value="{{$case_id}}">
             <input type="hidden" name="system" value="{{$system}}">
           <input name="file" id="poster" type="file" class="form-control">
             <br>
           <input type="submit"  value="Submit" class="btn btn-success">
        </div>
        </form>


@endsection
