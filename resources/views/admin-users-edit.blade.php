@extends('layouts.admin')


<title>{{ env('APP_NAME_FULL') }} - Edit User</title>

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
                    <input type="email" name="email" class="form-control" value="{{$user['email']}}" />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label text-left col-sm-4">Admin (1 admin, 0 user)</label>
                <div class="col-sm-8">
                    <input type="text" name="is_admin" class="form-control" value="{{$user['is_admin']}}" />
                </div>
            </div>
            
            <div class="form-group">
                <label class="control-label text-left col-sm-4">Block (1 blocked, 0 no block)</label>
                <div class="col-sm-8">
                    <input type="text" name="is_block" class="form-control" value="{{$user['is_block']}}" />
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
                    <input type="password" class="form-control" name="password" value="" placeholder="Fill in to save changes" />
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
