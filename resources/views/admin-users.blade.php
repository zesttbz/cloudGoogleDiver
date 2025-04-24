@extends('layouts.admin')


<title>{{ env('APP_NAME_FULL') }} - User</title>

@section('css')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
<style>
    .users-page {
        min-height: calc(100vh - 300px);
    }
</style>
@endsection

@section('content')
<div class="users-page">
    <div class="container">
        <div class="mt-5 mb-5">
            <h1>User list</h1>
            <hr>
            <table class="table table-bordered datatable">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width: 130px; text-align: center">Option</th>
                    </tr>
                </thead>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td class="text-center">
                        @if (!$user->is_admin)
                        <a class="btn btn-outline-danger btn-sm" href="/admin/users/delete/{{ $user->id }}"
                            onclick="return confirm('Want to delete this user? Deleting a user will also delete all uploaded files')">
                            Delete
                        </a>
                      	<a class="btn btn-outline-danger btn-sm" href="/admin/users/edit/{{ $user->id }}">
                            Edit
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready( function () {
        $('.datatable').DataTable();
    } );
</script>
@endsection