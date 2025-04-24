@extends('layouts.admin')


<title>{{ env('APP_NAME_FULL') }} - Drive Account</title>

@section('css')
{{-- <meta name="google-signin-client_id" content="{{ config('services.google.client_id') }}"> --}}
<link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
<style>
    .account-page {
        min-height: calc(100vh - 300px);
    }
</style>
@endsection

@section('content')
<div class="account-page">
    <div class="container">
        <div class="mt-5 mb-5">
            <div class="clearfix">
                <h1 class="float-left">List Drive accounts</h1>
                <div class="float-right">
                    {{-- <div id="google-login-button"></div> --}}
                    <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modal-create-account">Add account</button>
                </div>
            </div>
            <hr>
            <table class="table table-bordered text-center datatable">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Add date</th>
                        <th>Status</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                @foreach ($accounts as $account)
                <tr>
                    <td>{{ $account->id }}</td>
                    <td>{{ $account->name }}</td>
                    <td>{{ $account->email }}</td>
                    <td>{{ $account->created_at }}</td>
                    <td>
                        @if ($account->is_active)
                        <a onclick="return confirm('Want to deactivate this account?')"
                            href="/admin/google-accounts/update/{{ $account->id }}" class="btn btn-sm btn-success">
                            Enable
                        </a>
                        @else
                        <a onclick="return confirm('Want to activate this account?')"
                            href="/admin/google-accounts/update/{{ $account->id }}" class="btn btn-sm btn-secondary">
                            Disable
                        </a>
                        @endif
                    </td>
                    <td>
                        <a onclick="return confirm('Want to delete this account?')"
                            href="/admin/google-accounts/delete/{{ $account->id }}"
                            class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-create-account" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add account</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="/admin/google-accounts" method="post">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input class="form-control" type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Refresh token</label>
                        <textarea class="form-control" name="refresh_token" required cols="30" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
{{-- <script src="https://apis.google.com/js/platform.js?onload=renderButton" async defer></script>
<script>
    function renderButton() {
      gapi.signin2.render('google-login-button', {
        'scope': 'profile email https://www.googleapis.com/auth/drive',
        'width': 200,
        'height': 30,
        'longtitle': true,
        'theme': 'dark',
        'onsuccess': account => {
            console.log('account', account)
        },
        'onfailure': error => {
            console.log('error', error)
        }
      });
    }
</script> --}}

<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready( function () {
        $('.datatable').DataTable({
            "columns": [
                null,
                null,
                null,
                null,
                { "orderable": false },
                { "orderable": false },
            ]
        });
    } );
</script>
@endsection