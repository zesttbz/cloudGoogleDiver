@extends('layouts.app')

<title>{{ env('APP_NAME_FULL') }} - Free Cloud Upload</title>

@section('css')
<style>
    .jumbotron {
        background: url(/images/bg_cloud.jpg);
        min-height: calc(100vh - 300px);
    }
</style>
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h1>Hello!</h1>
                <h2>{{ env('APP_NAME_FULL') }} - Free data storage service</h2>
                <p>{{ env('APP_NAME') }} using Google's server system, applying cloud computing techniques to store
                     file. Uploaded files are stored for free, with unlimited storage, support for watching videos online
                     line. The fastest file download speed from Google servers.</p>
            </div>
            <div class="col-md-6">
              Advertising here
          
          
          </div>
        </div>
    </div>
</div>
@endsection