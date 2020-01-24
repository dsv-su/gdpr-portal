@extends('layouts.master_swe')

@section('content')
    <h5>GDRP Portal - Välkommen {{ $gdpr_user }}  <span style="float:right; font-size: 15px">Antal tillgängliga system: <code style="font-size: 20px">{{ $systems }}</code></span></h5>
    <!-- Form -->
    <form action="{{ route('search') }}" method="post" id="form">
        {{ csrf_field() }}
        <div class="a">
            <input pattern="^(19|20)?[0-9]{6}[- ]?[0-9]{4}$" type="text" id="gdpr_pnr" name="gdpr_pnr" placeholder="Personnummer" />
            <label for="gdpr_pnr">Personnummer</label>
            <div class="requirements">
                Måste vara i formatet ÅÅMMDD-NNNN
            </div>
        </div>
        <div class="a">
            <input type="email" id="gdpr_email" name="gdpr_email" placeholder="Epostadress" />
            <label for="gdpr_email">Epost</label>
            <div class="requirements">
                Måste vara en epostadress.
            </div>
        </div>
        <div class="a">
            <input type="text" id="gdpr_uid" name="gdpr_uid" placeholder="AnvändarId" />
            <label for="gdpr_uid">AnvändarId</label>
            <div class="requirements">
                Måste vara ett användarId.
            </div>
        </div>
        <div class="b">
            <button type="submit" class="btn btn-outline-primary">Skicka</button>
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
                <th scope="col"><i class="fas fa-barcode"></i> ÄrendeId:</th>
                <th scope="col"><i class="fas fa-search"></i> Förfrågan</th>
                <th scope="col"><i class="fas fa-spinner"></i> Status</th>
                <th scope="col"><i class="fas fa-file-upload"></i> Registrator</th>
                <th scope="col"><i class="far fa-trash-alt"></i> Radera</th>
                <th scope="col"><i class="fas fa-download"></i> Ladda ner</th>
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
                                                class="progress-bar bg-danger"
                                            @endif
                                            role="progressbar" style="width: {{ $plugin->download_status }}%;" aria-valuenow="{{ $plugin->download_status }}" aria-valuemin="0" aria-valuemax="100">{{$plugin->plugin_name}}:
                                            @if ($plugin->status == 200)
                                                {{ $plugin->download_status }}%
                                            @elseif ($plugin->status == 204)
                                                Personen kan inte hittas
                                            @elseif ($plugin->status == 400)
                                                Systemfel
                                            @endif
                                            </div>
                                     </div>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @if ( $case->registrar == 1)
                                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>Skickat: {{ $case->sent_registrar }}
                            @elseif ($case->registrar == 0 && $case->download >= 2 && $case->downloaded == 1)
                                <i class="fas fa-times"></i>  <a href="{{ route('send' , ['id'=> $case->id]) }}" class="btn btn-outline-success btn-sm" type="button">Skicka</a>
                            @else
                                <i class="fas fa-times"></i>
                            @endif

                        </td>
                        <td>
                            @if ($case->downloaded == 1)
                                <a href="{{ route('delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> Radera</a>
                            @endif
                            <a href="{{ route('dev_delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> ForceDelete</a>
                        </td>
                        <td>
                            @if ($case->status_flag == 0)
                                <span class="badge badge-danger">Misslyckades</span>
                            @elseif ($case->downloaded == 1)
                                    <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-check"></i> Nerladdad</a>
                            @elseif ($case->download_status > 0)
                                 <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-download"></i> Ladda ner</a>
                            @elseif ($case->download_status == 0 && $case->progress == 0)
                                 <span class="badge badge-warning">Inget att ladda ner</span>
                            @elseif ($case->progress == 1)
                                     <button class="btn btn-primary" type="button" disabled>
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                              Laddar ner...
                                     </button>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
                </tbody>
        </table>
    </div>
    <!-- The Modal -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Varning!</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    Portalen är i testläge och alla sökningar loggas!
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Stäng</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(window).on('load', function() {
            $('#myModal').modal('show');
        });
    </script>

    <script>
        $(document).ready(function () {
            var auto_refresh = setInterval(
                function() {
                    $('#cases').load('<?php echo url('/status');?>').fadeIn("slow");
                },1000);

        });
    </script>
@endsection
