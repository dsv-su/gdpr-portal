@extends('layouts.master_swe')

@section('content')
    <div class="container">
        @if(session()->get('message'))
            <div class="alert alert-success">
                {{ session()->get('message') }}
            </div>
        @endif
            <h3>GDPR Upload</h3>
            <form method="post" action="{{route('upload')}}" enctype="multipart/form-data"
                  class="dropzone" id="dropzone">
                @csrf
            </form>
            <script type="text/javascript">
                Dropzone.options.dropzone =
                    {
                        maxFilesize: 12,
                        renameFile: function(file) {
                            var dt = new Date();
                            var time = dt.getTime();
                            return time+file.name;
                        },
                        acceptedFiles: ".jpeg,.jpg,.png,.gif",
                        addRemoveLinks: true,
                        timeout: 5000,
                        success: function(file, response)
                        {
                            console.log(response);
                        },
                        error: function(file, response)
                        {
                            return false;
                        }
                    };
            </script>
@endsection
