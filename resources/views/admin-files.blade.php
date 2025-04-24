@extends('layouts.admin')


<title>{{ env('APP_NAME_FULL') }} - File List</title>

@section('css')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
<style>
    .files-page {
        min-height: calc(100vh - 300px);
    }

    .media img {
        width: 40px;
        height: 40px;
        border-radius: 100%;
        border: 1px solid #d7d7d7;
    }
</style>
@endsection

@section('content')
<div class="files-page">
    <div class="container">
        <div class="mt-5 mb-5">
            <h1>File List</h1>
            <hr>
            <table class="table table-bordered datatable">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>File name</th>
                        <th>User</th>
                        <th>File size</th>
                        <th>Upload date</th>
                        <th class="text-center">Download</th>
                        <th class="text-center">Delete</th>
                    </tr>
                </thead>
                @foreach ($files as $file)
                <tr>
                    <td>{{ $file->id }}</td>
                    <td>
                        <div>{{ $file->name }}</div>
                        <small>{{ $file->file_type }}</small>
                    </td>
                    <td data-sort="{{ $file->user->id }}">
                        <div class="media">
                            <img class="mr-2" src="{{ $file->user->image ?: '/images/avatar.png' }}">
                            <div class="media-body">
                                <div>{{ $file->user->name }}</div>
                                <small>{{ $file->user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td data-sort="{{ $file->file_size }}">{{ number_format($file->file_size, 0, ',', '.') }} KB</td>
                    <td>{{ $file->created_at }}</td>
                    <td class="text-center" data-sort="{{ $file->total_download }}">
                        {{ number_format($file->total_download, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <a class="btn btn-outline-danger btn-sm" href="/admin/files/delete/{{ $file->id }}"
                            onclick="return confirm('Do you want to delete this file?')">
                            Delete
                        </a>
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
        $('.datatable').DataTable({
            "order": [[ 0, "desc" ]],
            "columns": [
                null,
                null,
                null,
                null,
                null,
                null,
                { "orderable": false },
            ]
        });
    } );
</script>
@endsection