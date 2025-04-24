@extends('layouts.app')


<title>{{ env('APP_NAME_FULL') }} - Account</title>

@section('css')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
<style>
    .dashboard-page {
        min-height: calc(100vh - 300px);
    }
</style>
@endsection

@section('content')



<div class="container">
    <div id="profile">
        
        @if (session('success'))
        {{-- <div class="alert alert-success" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>{{ __('Success!') }}</strong>
        </div> --}}
        <div class="alert alert-success">
            <ul>
                @foreach (session('success') as $success)
                    <li>{{ $success }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="post" class="form-horizontal">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <br>
            <div class="form-group">
                <label class="control-label text-left col-sm-2">{{ __('Name') }}:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" name="name" value="{{$user['name']}}" placeholder="{{ __('Display name') }}" />
                </div>
            </div>
            
            
            
            <div class="form-group">
                <label class="control-label text-left col-sm-2">{{ __('Email') }}:</label>
                <div class="col-sm-8">
                    <input type="email" name="email" class="form-control" value="{{$user['email']}}" readonly='' />
                </div>
            </div>
            
            {{--<div class="form-group">
                <label class="control-label text-left col-sm-2">{{ __('Gender') }}:</label>
                <div class="col-sm-10">
                    <label class="radio-inline"><input type="radio" name="gender" value="1"{{ $user['gender']===1 ? ' checked' : ''}}/>{{ __('Male') }}</label>
                    <label class="radio-inline"><input type="radio" name="gender" value="0"{{ $user['gender']===0 ? ' checked' : ''}}/>{{ __('Female') }}</label>
                </div>
            </div>--}}
            <div class="form-group">
                <label class="control-label text-left col-sm-2">{{ __('Registration Date') }}:</label>
                <div class="col-sm-8">
                    <h5 name="created">{{$user['created_at']}}</h5>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label text-left col-sm-2">{{ __('Update day') }}:</label>
                <div class="col-sm-8">
                    <h5 name="created">{{$user['updated_at']}}</h5>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label text-left col-sm-2">{{ __('New password') }}:</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" name="password" value="" placeholder="Điền để lưu thay đổi" />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-outline-success">{{ __('Save') }}</button>
                </div>
            </div>
        </form>
    </div>

    <div class='loading' style='display: none;'></div>
    <div class='alert alert-success msg-success' role='alert' style='display: none;'><strong>{{ __('Success!') }} </strong><span></span></div>
    <div class='alert alert-danger msg-danger' role='alert' style='display: none;'><strong>{{ __('Failure!') }} </strong><span></span></div>
    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <div class="clearfix"></div>
</div>




@endsection
