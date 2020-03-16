<table class="table table-sm table-fixed" id="cases">
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
                                    role="progressbar" style="width: {{ $plugin->progress_status }}%;" aria-valuenow="{{ $plugin->progress_status }}" aria-valuemin="0" aria-valuemax="100">{{$plugin->plugin_name}}:
                                    @if ($plugin->status == 204)
                                    Personen kan inte hittas
                                    @elseif ($plugin->status == 400 or $plugin->status == 404)
                                        Systemfel
                                    @elseif ($plugin->status == 300)
                                        Väntar
                                    @elseif ($plugin->status == 301)
                                        Kontaktar
                                    @elseif ($plugin->status == 307)
                                        Inte vald
                                    @endif

                                </div>
                            </div>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @if ( $case->registrar == 1)
                            <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b> Skickat: {{ $case->sent_registrar }}
                        @elseif ($case->registrar == 0 && $case->status_flag == 3 && $case->downloaded == 1)
                            <i class="fas fa-times"></i>  <a href="{{ route('send' , ['id'=> $case->id]) }}" class="btn btn-outline-success btn-sm" type="button">Skicka</a>
                         @else
                            <i class="fas fa-times"></i>
                        @endif
                    </td>
                    <td>@if ($case->downloaded == 1)
                            <a href="{{ route('delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> Radera</a>
                        @endif
                        <a href="{{ route('dev_delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> ForceDelete</a>
                    </td>
                    <td>
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
