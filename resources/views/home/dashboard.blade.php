@extends('layouts.master')

@section('content')
    <h5>GDRP - Welcome {{ $gdpr_user }}</h5>
    <form action="{{ route('search') }}" method="post" id="form">
        {{ csrf_field() }}

            <label for="gdpr-form" class="text-primary">Search according to one of the following criteria:</label>
            <br>
                <div class="a">
                    <input pattern="^(19|20)?[0-9]{6}[- ]?[0-9]{4}$" type="text" id="gdpr_pnr" name="gdpr_pnr" placeholder="Personal ID" />
                    <label for="gdpr_pnr">Personal ID</label>
                    <div class="requirements">
                        Must be in format YYMMDD-NNNN
                    </div>

                </div>
                <div class="a">
                    <input type="email" id="gdpr_email" name="gdpr_email" placeholder="Email Address" />
                    <label for="gdpr_email">Email Address</label>
                    <div class="requirements">
                        Must be a valid email address.
                    </div>
                </div>
                <div class="a">
                    <input type="text" id="gdpr_uid" name="gdpr_uid" required placeholder="UserId" />
                    <label for="gdpr_uid">UserId</label>
                    <div class="requirements">
                        Must be a valid uid.
                    </div>
                </div>
                <div class="b">
                <button type="submit" class="btn btn-outline-primary">Start Request</button>
                    <div class="req">
                        Must
                    </div>
                </div>

    </form>
    <!-- -->
    <div class="row row-no-gutters" id="cases">
        <table class="table table table-sm table-fixed">
            <thead class="table-primary">
            <tr>
                <th scope="col"><i class="fas fa-barcode"></i> Case Id:</th>
                <th scope="col"><i class="fas fa-search"></i> Request</th>
                <th scope="col"><i class="fas fa-spinner"></i> Status</th>
                <th scope="col"><i class="fas fa-file-upload"></i> Registrar</th>
                <th scope="col"><i class="far fa-trash-alt"></i> Delete</th>
                <th scope="col"><i class="fas fa-download"></i> Download</th>
            </tr>
            </thead>
            @foreach ($cases as $case)
                <tbody>
                @if ($case->visability == 1)
                    <tr>
                        <td scope="row">{{ $case->case_id }}</td>
                        <td class="small text-center">{{ $case->request_pnr }}<br>{{$case->request_email}}<br>{{$case->request_uid}}</td>
                        <td>
                            @if ($case->status_moodle_test == 200)
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $case->download_moodle_test }}%;" aria-valuenow="{{ $case->download_moodle_test }}" aria-valuemin="0" aria-valuemax="100">Ilearn2Test {{ $case->download_moodle_test }}%</div>
                            </div>
                            @endif
                            @if ($case->status_moodle_test == 404)
                                 <div class="progress">
                                 <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $case->download_moodle_test }}%;" aria-valuenow="{{ $case->download_moodle_test }}" aria-valuemin="0" aria-valuemax="100">Ilearn2Test</div>
                                 </div>
                                @endif
                            @if ($case->status_scipro_dev == 200)
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: {{ $case->download_scipro_dev }}%;" aria-valuenow="{{ $case->download_scipro_dev }}" aria-valuemin="0" aria-valuemax="100">Scipro-dev {{ $case->download_scipro_dev }}%</div>
                            </div>
                            @endif
                            @if ($case->status_scipro_dev == 204)
                             <div class="progress">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $case->download_scipro_dev }}%;" aria-valuenow="{{ $case->download_scipro_dev }}" aria-valuemin="0" aria-valuemax="100">Scipro-dev: User not found</div>
                                </div>
                            @endif
                            @if ($case->status_scipro_dev == 400)
                                 <div class="progress">
                                   <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $case->download_scipro_dev }}%;" aria-valuenow="{{ $case->download_scipro_dev }}" aria-valuemin="0" aria-valuemax="100">Scipro-dev: Client Error</div>
                                 </div>
                            @endif
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
                                <a href="{{ route('dev_delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> ForceDelete</a>
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
    <script>
        $(document).ready(function () {
            var auto_refresh = setInterval(
                function() {
                    $('#cases').load('<?php echo url('/status');?>').fadeIn("slow");
                },1000);

        });
    </script>
@endsection
