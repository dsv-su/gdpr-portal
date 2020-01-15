@extends('layouts.master')

@section('content')
    <h5>GDRP Portal - Welcome {{ $gdpr_user }}  <span style="float:right; font-size: 15px">Available number of Systems: <code style="font-size: 20px">{{ $systems }}</code></span></h5>
    <!-- Form -->
    <form action="{{ route('search') }}" method="post" id="form">
        {{ csrf_field() }}
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
            </div>
        </div>
    </form>
    <br>
    <!-- End form-->
    <div class="row row-no-gutters"id="cases" >
        <table class="table table-sm table-fixed">
            <thead>
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
                        <td class="small text-center">@if (!$case->request_pnr==null){{ $case->request_pnr }} <br> @endif @if (!$case->request_email==null) {{$case->request_email}} <br> @endif @if (!$case->request_uid==null) {{$case->request_uid}} @endif</td>
                        <td>
                            @foreach ($pluginstatuses as $plugin)
                                @if ($case->id == $plugin->searchcase_id)
                                    <div class="progress">
                                         <div
                                            @if ($plugin->status == 200)
                                                class="progress-bar"
                                            @elseif ($plugin->status == 204)
                                                class="progress-bar bg-warning"
                                            @elseif ($plugin->status == 400)
                                                class="progress-bar bg-danger
                                            @endif
                                            role="progressbar" style="width: {{ $plugin->download_status }}%;" aria-valuenow="{{ $plugin->download_status }}" aria-valuemin="0" aria-valuemax="100">{{$plugin->plugin_name}}
                                            @if ($plugin->status == 200)
                                                {{ $plugin->download_status }}%
                                            @elseif ($plugin->status == 204)
                                                User not found
                                            @elseif ($plugin->status == 400)
                                                System error
                                            @endif
                                        </div>
                                     </div>
                                @endif
                            @endforeach
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
                            @if ($case->downloaded == 1)
                                <a href="{{ route('delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</a>
                            @endif
                            <a href="{{ route('dev_delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> ForceDelete</a>
                        </td>
                        <td>
                            @if ($case->status_flag == 0)
                                <span class="badge badge-danger">Failed</span>
                            @elseif ($case->downloaded == 1)
                                    <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-check"></i> Downloaded</a>
                            @elseif ($case->download_status == 100)
                                 <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-download"></i> Download</a>
                            @elseif ($case->download_status < 100 && $case->progress == 0)
                                 <span class="badge badge-warning">Nothing to download</span>
                            @elseif ($case->progress == 1)
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
