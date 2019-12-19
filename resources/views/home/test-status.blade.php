<td id="cases" >
    @if ($case->status_moodle_test == 200)
        <div class="progress">
            <div class="progress-bar" role="progressbar" style="width: {{ $case->download_moodle_test }}%;" aria-valuenow="{{ $case->download_moodle_test }}" aria-valuemin="0" aria-valuemax="100">Ilearn2Test {{ $case->download_moodle_test }}%</div>
        </div>
    @endif
    @if ($case->status_moodle_test == 404)
        <div class="progress">
            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $case->download_moodle_test }}%;" aria-valuenow="{{ $case->download_moodle_test }}" aria-valuemin="0" aria-valuemax="100">Ilearn2Test: User not found</div>
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
</td>
