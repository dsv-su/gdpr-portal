@extends('layouts.master')

@section('content')
    <h5>GDRP</h5>
    <div class="row row-no-gutters">
            <table class="table table-sm">
                <tbody>
                <tr>
                    <td class="small">GDRP-officer: GDPR officer</td><td></td>
                    <td class="small">Available systems: 2</td><td></td><td></td><td></td>
                    <td><a href="{{ route('new_search') }}" class="btn btn-outline-primary" role="button">New search</a></td>
                </tr>
                </tbody>
            </table>
    </div>

    <div class="row row-no-gutters">
            <table class="table table-bordered table table-sm">
                <thead class="table-primary">
                <tr>
                    <th scope="col"><i class="fas fa-barcode"></i> Case Id:</th>
                    <th scope="col"><i class="fas fa-search"></i> Request</th>
                    <th scope="col"><i class="fas fa-spinner"></i> Status</th>
                    <th scope="col"><i class="fas fa-file-upload"></i> Registrar</th>
                    <th scope="col"><i class="fas fa-download"></i> Download</th>
                </tr>
                </thead>
                @foreach ($cases as $case)
                <tbody>
                <tr>
                    <th scope="row">{{ $case->case_id }}</th>
                    <td>{{ $case->request }}</td>
                    <td>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: {{ $case->status }}%;" aria-valuenow="{{ $case->status }}" aria-valuemin="0" aria-valuemax="100">{{ $case->status }}%</div>
                        </div>
                    </td>
                    <td>
                        @if ( $case->registrar == 1)
                            <i class="fas fa-check"></i><button class="btn btn-success btn-sm" type="button" disabled>Send</button>
                        @elseif ($case->registrar == 0 && $case->download < 2)
                            <i class="fas fa-times"></i>  <button class="btn btn-success btn-sm" type="button" disabled>Send</button>
                        @elseif ($case->registrar == 0 && $case->download == 2)
                            <i class="fas fa-times"></i>  <button class="btn btn-success btn-sm" type="button">Send</button>
                        @endif
                    </td>
                    <td>
                        @if ($case->download == 2)
                            <button class="btn btn-primary" type="button">Dowload</button>
                        @else
                        <button class="btn btn-primary" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Downloading...
                        </button>
                        @endif
                    </td>
                </tr>

                @endforeach
                </tbody>
            </table>
        </div>
            <br>

    </form>

@endsection
