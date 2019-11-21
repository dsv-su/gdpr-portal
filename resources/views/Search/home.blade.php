@extends('layouts.master')

@section('content')
    <h4>GDRP request</h4>
        <div class="row row-no-gutters">
            <div class="col-2 small">GDRP-officer:</div>
            <div class="col-4">
                <table class="table table-sm table-bordered">
                    <tbody>
                    <tr>
                        <td class="small">GDPR officer</td>
                    </tr>
                    <tr>
                        <td class="small">Email of GDPR officer</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-2 small">Available systems:</div>
            <div class="col-1">
                <table class="table table-sm table-bordered">
                    <tbody>
                    <tr>
                        <td class="text-center"><h3>2</h3></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-8"></div>
            <div class="col"></div>
        </div>
        <form action="{{ route('search') }}" method="post">
            {{ csrf_field() }}
            <div class="form-group border border p-5">
                <label for="gdpr-form" class="text-primary">Search according to one of the following criteria</label>
                <div class="form-row">
                        <div class="col-4">
                            <label>Personal ID number:</label>
                            <input class="form-control form-control-sm" type="text" name="personnr">
                            <small class="text-danger"></small>
                        </div>
                        <div class="col-4">
                            <label>Email:</label>
                            <input class="form-control form-control-sm" type="text" name="gdpr_email">
                            <small class="text-danger"></small>
                        </div>
                        <div class="col-4">
                            <label>User ID:</label>
                            <input class="form-control form-control-sm" type="text" name="gdpr_userid">
                            <small class="text-danger"></small>
                        </div>
                </div>
           <br>
                <button type="submit" class="btn btn-primary mb-2">Start search</button>
            </form>




@endsection
