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
                <td>@if ($case->download > 2)
                        <a href="{{ route('delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> Delete</a>
                    @endif
                    <a href="{{ route('dev_delete', ['id'=>$case->id ]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i> ForceDelete</a>
                </td>
                <td>
                    @if ($case->download == 2)
                        <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-download"></i> Download</a>
                    @elseif ($case->download >= 1 && $case->download < 3 && ($case->status_moodle_test == 204 or $case->status_scipro_dev == 204))
                        <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-download"></i> Download</a>
                    @elseif ($case->download > 2)
                        <a href="{{ route('download', ['id'=>$case->id ]) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-check"></i> Downloaded</a>
                    @elseif ($case->download <2 && $case->status_processed <2 && ($case->status_scipro_dev == 200 or $case->status_moodle_test == 200))
                        <button class="btn btn-primary" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Downloading...
                        </button>
                    @elseif ($case->download >=1 && $case->status_scipro == 204 or $case->status_moodle_test == 204)
                        <span class="badge badge-warning">Nothing to download</span>
                    @else
                        <span class="badge badge-danger">Failed</span>

                    @endif
                </td>
            </tr>
        @endif
        @endforeach
        </tbody>
</table>
