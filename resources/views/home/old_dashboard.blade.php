@extends('layouts.test-master')

@section('content')
    <h5>GDRP</h5>
    <div class="row row-no-gutters">
            <table class="table table-sm">
                <tbody>
                <tr>
                    <td class="small">
                        GDRP-officer: Test Testuser
                    </td><td></td>
                    <td class="small">Available systems: 2</td><td></td><td></td><td></td>
                </tr>
                </tbody>
            </table>
    </div>
<!-- -->
    <form action="{{ route('search') }}" method="post" id="form">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="gdpr-form" class="text-primary">Search according to one of the following criteria</label>
            <div class="form-row">
                <div class="col-3">
                    <label for="gdpr_pnr">Personal ID number:</label>
                    <input class="form-control form-control-sm" type="text" name="gdpr_pnr" id="gdpr_pnr" data-field="gdpr_pnr"  />
                    <div class="valid-message"></div>
                </div>
                <div class="col-3">
                    <label for="gdpr_email">Email:</label>
                    <input class="form-control form-control-sm" type="text" name="gdpr_email" id="gdpr_email" data-field="gdpr_email">
                    <div class="valid-message"></div>
                </div>
                <div class="col-3">
                    <label>User ID:</label>
                    <input class="form-control form-control-sm" type="text" name="gdpr_userid">
                    <small class="text-danger"></small>
                </div>
                <div class="col-3">
                    <br>
                    <button type="submit" class="btn btn-outline-primary">Start search</button>
                    <small class="text-danger"></small>
                </div>
            </div>
        </div>
    </form>

    <!-- -->
    <div class="row row-no-gutters" id="cases">
            <table class="table table-bordered table table-sm table-fixed">
                <thead class="table-primary">
                <tr>
                    <th scope="col"><i class="fas fa-barcode"></i> Case Id:</th>
                    <th scope="col"><i class="fas fa-search"></i> Request</th>
                    <th scope="col"><i class="fas fa-spinner"></i> Status</th>
                    <th scope="col"><i class="fas fa-file-upload"></i> Registrar</th>
                    <th scope="col"><i class="far fa-trash-alt"></i></i> Delete</th>
                    <th scope="col"><i class="fas fa-download"></i> Download</th>
                </tr>
                </thead>
                @foreach ($cases as $case)
                <tbody>
                @if ($case->visability == 1)
                <tr>
                    <th scope="row">{{ $case->case_id }}</th>
                    <td class="small text-center">{{ $case->request_pnr }}<br>{{$case->request_email}}<br>{{$case->request_uid}}</td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $case->status_moodle_test }}%;" aria-valuenow="{{ $case->status_moodle_test }}" aria-valuemin="0" aria-valuemax="100">Ilearn2Test {{ $case->status_moodle_test }}%</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $case->status_scipro_dev }}%;" aria-valuenow="{{ $case->status_scipro_dev }}" aria-valuemin="0" aria-valuemax="100">Scipro-dev {{ $case->status_scipro_dev }}%</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100">Daisy 0% Failed</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%;" aria-valuenow="5" aria-valuemin="0" aria-valuemax="100">Ilearn2 0% Failed</div>
                        </div>
                    </td>
                    <td>
                        @if ( $case->registrar == 1)
                            <i class="fas fa-check"></i><button class="btn btn-success btn-sm" type="button" disabled>Send</button>
                        @elseif ($case->registrar == 0 && $case->download < 2)
                            <i class="fas fa-times"></i>  <button class="btn btn-success btn-sm" type="button" disabled>Send</button>
                        @elseif ($case->registrar == 0 && $case->download >= 2)
                            <i class="fas fa-times"></i>  <button class="btn btn-success btn-sm" type="button">Send</button>
                        @endif
                    </td>
                    <td>
                        @if ($case->download > 2)
                            <a href="{{ route('delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</a>
                        @endif

                    </td>
                    <td>
                        @if ($case->download == 2)
                            <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-download"></i> Download</a>
                            @elseif ($case->download > 2)
                                <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-check"></i> Downloaded</a>
                        @else
                        <button class="btn btn-primary" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Downloading...
                        </button>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
                </tbody>
            </table>
        </div>
            <br>

    </form>
<script>
    $(document).ready(function () {
        var form = $('#form').formValid({
            fields: {
                "gdpr_email": {
                    "required": false,
                    "tests": [
                        {
                            "type": "null",
                            "message": "Not entered an email"
                        },
                        {
                            "type": "email",
                            "message": "Your entry is incorrect! Enter a valid email."
                        }
                    ]
                },
                "gdpr_pnr": {
                    "required": false,
                    "tests": [
                        {
                            "regex": "^(19|20)?[0-9]{6}[- ]?[0-9]{4}$",
                            "message": "Your entry is incorrect! Enter a pnr in the following format YYMMDD-XXXX"
                        }
                    ]
                }
            }
        });

        form.keypress(300);

        $('button[type="submit"]').click(function() {
            form.test();
            if (form.errors() == 0) {
                return true;
            }
            return false;
        });
        //
        var auto_refresh = setInterval(
            function() {
                $('#cases').load('<?php echo url('/status');?>').fadeIn("slow");
            },1000);

    });
</script>
@endsection
