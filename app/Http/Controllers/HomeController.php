<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Apis\GoogleApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function file($fileCode)
    {
        $file = File::where('code', $fileCode)->with(['uploadedFiles', 'uploadedFiles.account'])->firstOrFail();
        $uploadedFiles = [];
        foreach ($file->uploadedFiles as $item) {
            $api = new GoogleApi($item->account);
            if ($token = $api->getToken()) {
                $item->token = $token;
                $uploadedFiles[] = [
                    'id' => $item->google_file_id,
                    'token' => $token,
                    'name' => $file->name,
                    'type' => $file->file_type,
                    'size' => $file->file_size,
                ];
            }
        }

        return view('file', ['file' => $file, 'uploadedFiles' => $uploadedFiles]);
    }

    public function updateDownloadFile($fileCode)
    {
        $file = File::where('code', $fileCode)->firstOrFail();
        $file->increment('total_download');
    }
}
