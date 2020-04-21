@extends('layouts.master_swe')

@section('content')
    <div class="welcome">
        <h5>GDRP Portal - Välkommen {{ $gdpr_user }}  <span style="float:right; font-size: 15px">Antal tillgängliga system: <code style="font-size: 20px">{{ $systems }}</code></span></h5>
    </div>
        <div class="searchrequest">
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
            <button type="submit"  data-toggle="collapse" data-target="#collapse" id="send">&nbsp;&nbsp;&nbsp;Sök&nbsp;&nbsp;&nbsp;</button>
            <div class="req">
            </div>
        </div>
    </div>
    <br>
    <!-- Start collapsable div-->
    @if ($collapse == 0)
        <div class="searchrequest collapse show" id="collapse">
    @elseif ($collapse == 1)
        <div class="searchrequest collapse" id="collapse">
    @endif
            <div class="row">
            <div class="card-group" style="width: 95%;margin: 0 auto;">
                @foreach ($system_name as $title)
                <div class="col-xs-6">
                    <div class="card border-light" style="width: 9rem;">
                        <div class="card-header">
                            {{$title}}
                        </div>
                        @foreach ($plugins as $plugin)
                            @if( $title == $plugin->plugin)
                        <ul class="list-group list-group-flush">
                            <input type="hidden" name="{{ $plugin->name }}" value=1>

                            <li class="list-group-item">{{$plugin->name}} <input type="checkbox" id="{{ $plugin->name }}" name=" {{ $plugin->name }}" value=0 checked disabled></li>

                        </ul>
                                <datalist id="huge_list">
                                </datalist>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            </div>
            <!-- end collapse div -->
        </div>
    </form>

    <br>
    <!-- End Collapseable form -->

    <!-- Start collapsable div-->
    @if ( $init == 1)
    <div class="row row-no-gutters collapse show" id="collapse_case">
    @elseif ($init == 0)
    <div class="row row-no-gutters collapse" id="collapse_case">
    @endif
        <table class="table table-sm table-fixed" id="cases">
            <thead>
            <tr class="row m-0">
                <th class="d-inline-block col-1"></i> ÄrendeId:</th>
                <th class="d-inline-block col-2 text-center"><i class="fas fa-search"></i>Förfrågan</th>
                <th class="d-inline-block col-3 text-center"><i class="fas fa-spinner"></i> Status</th>
                <th class="d-inline-block col-2 text-center"><i class="fas fa-file-upload"></i> Registrator</th>
                <th class="d-inline-block col-2"><i class="far fa-trash-alt"></i> Radera</th>
                <th class="d-inline-block col-2"><i class="fas fa-download"></i> Ladda ner</th>
            </tr>
            </thead>
            @foreach ($cases as $case)
                <tbody>
                @if ($case->visability == 1)
                    <tr class="row m-0">
                        <td class="d-inline-block col-1">{{ $case->case_id }}</td>
                        <td class="d-inline-block col-2 small text-center">@if (!$case->request_pnr==null){{ $case->request_pnr }} <br> @endif @if (!$case->request_email==null) {{$case->request_email}} <br> @endif @if (!$case->request_uid==null) {{$case->request_uid}} @endif</td>
                        <td class="d-inline-block col-3 text center">
                            @foreach ($pluginstatuses as $plugin)
                                @if ($case->id == $plugin->searchcase_id)
                                    <div class="progress" id="status">
                                         <div
                                            @if ($plugin->status == 200)
                                                class="progress-bar"
                                            @elseif ($plugin->status == 204)
                                                class="progress-bar bg-warning"
                                            @elseif ($plugin->status == 400 or $plugin->status == 404)
                                                class="progress-bar bg-danger"
                                            @elseif ($plugin->status == 409)
                                                class="progress-bar progress-bar-striped bg-danger"
                                            @elseif ($plugin->status == 300)
                                                class="progress-bar bg-success"
                                            @elseif ($plugin->status == 301)
                                                class="progress-bar bg-success"
                                            @elseif ($plugin->status == 307)
                                                class="progress-bar bg-info"
                                            @endif
                                            role="progressbar" style="width: {{ $plugin->progress_status }}%;" aria-valuenow="{{ $plugin->progress_status }}" aria-valuemin="0" aria-valuemax="100">{{$plugin->plugin_name}}
                                            @if ($plugin->status == 204)
                                               : Personen kan inte hittas
                                            @elseif ($plugin->status == 400 or $plugin->status == 404)
                                               : Systemfel
                                            @elseif ($plugin->status == 300)
                                               : Väntar
                                             @elseif ($plugin->status == 301)
                                               : Kontaktar
                                             @elseif ($plugin->status == 307)
                                               : Inte vald
                                             @endif
                                            </div>
                                     </div>
                                @endif
                            @endforeach
                        </td>
                        <td class="d-inline-block col-2 small text-center">
                            @if ( $case->registrar == 1)
                                <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>Skickat: {{ $case->sent_registrar }}
                            @elseif ($case->registrar == 0 && $case->status_flag == 3 && $case->downloaded == 1)
                                <i class="fas fa-times"></i>  <a href="{{ route('send' , ['id'=> $case->id]) }}" class="btn btn-outline-success btn-sm" type="button">Skicka</a>
                            @else
                                <i class="fas fa-times"></i>
                            @endif

                        </td>
                        <td class="d-inline-block col-2">
                            @if ($case->downloaded == 1)
                                <a href="{{ route('delete', ['id'=>$case->id ]) }}" id="delete" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> Radera</a>
                            @endif
                            <a href="{{ route('dev_delete', ['id'=>$case->id ]) }}" id="forcedelete" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> ForceDelete</a>
                        </td>
                        <td class="d-inline-block col-2">
                            @if ($case->status_flag == 0)
                                <span class="badge badge-danger">Misslyckades</span> <a href="{{ route('override', ['id'=>$case->id ]) }}" type="button" class="btn btn-outline-primary btn-sm">Override</a>
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
            <script>
                $(document).ready(function () {
                        <?php if ($init == 1) { ?>
                    var auto_refresh = setInterval(
                        function() {
                            $('#cases').load('<?php echo url('/status');?>').fadeIn("slow");
                        },1000);
                    <?php } ?>
                    $( "#gdpr_pnr" ).change(function() {
                        $('.collapse').show();
                        //
                        <?php foreach($plugins as $plugin) {
                            if (in_array($plugin->search,array(4,5,6,7))) { ?>
                                var plugin = document.getElementById('<?php echo $plugin->name ?>');
                                plugin.removeAttribute('disabled');
                        <?php }
                            } ?>
                        //
                    });
                    $( "#gdpr_email" ).change(function() {
                        $('.collapse').show();
                        //
                            <?php foreach($plugins as $plugin) {
                            if (in_array($plugin->search,array(2,3,6,7))) { ?>
                        var plugin = document.getElementById('<?php echo $plugin->name ?>');
                        plugin.removeAttribute('disabled');
                        <?php }
                        } ?>
                        //
                    });
                    $( "#gdpr_uid" ).change(function() {
                        $('.collapse').show();
                        //
                            <?php foreach($plugins as $plugin) {
                            if (in_array($plugin->search,array(1,3,5,7))) { ?>
                        var plugin = document.getElementById('<?php echo $plugin->name ?>');
                        plugin.removeAttribute('disabled');
                        <?php }
                        } ?>
                        //
                    });

                });
            </script>
@endsection
