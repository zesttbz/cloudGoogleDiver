@extends('layouts.app')

<title>{{ env('APP_NAME_FULL') }} - Dashboard</title>

@section('css')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
<style>
    .dashboard-page {
        min-height: calc(100vh - 300px);
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="container mt-5 mb-5">
        <div class="clearfix">
            <h1 class="float-left">File list</h1>
            {{-- <div class="float-right mt-2">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search">
                    <div class="input-group-append">
                        <span style="cursor: pointer" class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div> --}}
        </div>
        <hr>
        <div class="table-responsive-sm">
            <table class="table table-bordered datatable">
                <thead class="thead-dark">
                    <tr>
                        <th>Upload date</th>
                        <th>File name</th>
                        <th>Download</th>
                        <th>Size</th>
                        <th class="text-center">Option</th>
                    </tr>
                </thead>
                @if (count($files))
                @foreach ($files as $index => $file)
                <tr>
                    <td>{{ $file->created_at }}</td>
                    <td>{{ $file->name }}</td>
                    <td data-sort="{{ $file->total_download }}">{{ number_format($file->total_download, 0, ',', '.') }}
                    </td>
                    <td data-sort="{{ $file->file_size }}">{{ number_format($file->file_size, 0, ',', '.') }} KB</td>
                    <td class="text-center">
                        <button class="btn btn-success btn-sm btn-copy" data-toggle="tooltip" data-placement="top"
                            title="Copy" data-copy="{{ url('/file/' . $file->code) }}">
                            <i class="far fa-copy" data-copy="{{ url('/file/' . $file->code) }}"></i>
                        </button>
                        <a class="btn btn-warning btn-sm" target="_blank" href="/file/{{ $file->code }}" data-toggle="tooltip"
                            data-placement="top" title="Download">
                            <i class="fas fa-download"></i>
                        </a>
                        <a class="btn btn-danger btn-sm" href="/dashboard/file/{{ $file->id }}"
                            onclick="return confirm('Do you want to delete this file?')" data-toggle="tooltip"
                            data-placement="top" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="6">
                        <div class="text-center">You have not uploaded any files yet</div>
                    </td>
                </tr>
                @endif
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
        $('[data-toggle="tooltip"]').tooltip();
        $('.btn-copy').on('click', function(element) {
            var text = $(element.target).attr('data-copy')
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();
            $(element.target).attr('data-original-title', 'Copied')
        })
        $('.datatable').DataTable({
            "order": [[ 0, "desc" ]],
            "columns": [
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