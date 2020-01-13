@extends('layouts.master')

@section('content')
    <h5>Plugin configurations</h5>
    @foreach($plugins as $plugin)
    <form action="/plugin_configuration/{{$plugin->id}}" method="POST" id="form">
        {{ csrf_field() }}

            <div class="c">
                <div>
                    <code><span style="font-size: 25px">{{ $plugin->id }}</span> {{$plugin->name}}</code>
                </div>
                <input type="text" id="pluginname" name="pluginname" value="{{$plugin->name}}" />
            </div>
            <div class="c">
                <div>
                    Client_id:
                </div>
                <input type="text" id="plugin_client_id" name="plugin_client_id" value="{{$plugin->client_id}}" />
            </div>
        <div class="c">
            <div>
                Client_secret:
            </div>
            <input type="text" id="plugin_client_secret" name="plugin_client_secret" value="{{$plugin->client_secret}}" />
        </div>
        <div class="c">
            <div>
                Auth_url:
            </div>
            <input type="text" id="plugin_auth_url" name="plugin_auth_url" value="{{$plugin->auth_url}}" />
        </div>
            <div class="c">
               <div>
                 Status:
               </div>
                <input type="text" id="pluginstatus" name="pluginstatus" value="{{$plugin->status}}" />
            </div>
            <div class="a">
                <div>
                    Modified:
                </div>
                <input type="text" id="pluginmodify" name="pluginmodify" placeholder="{{$plugin->updated_at}}" />
                <label for="pluginmodify">Pluginstatus</label>
            </div>
            <div class="b">
                <button type="submit" class="btn btn-outline-primary">Save</button>
                <div class="req">
                    Must
                </div>
            </div>

    </form>
        <br>
    @endforeach
@endsection
