<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Apis\GoogleApi;
use Illuminate\Http\Request;
use App\Models\GoogleAccount;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard(Request $request)
    {
        return view('dashboard', [
            'user' => $request->user(),
            'files' => $request->user()->files()->orderBy('id', 'desc')->get()
        ]);
    }

    public function showUploadForm()
    {
        return view('upload', [
            'accounts' => GoogleAccount::where('is_active', true)
                ->get()
                ->map(function ($account)
                {
                    return [
                        'id' => $account->id,
                        'token' => (new GoogleApi($account))->getToken()
                    ];
                })
            ]
        );
    }

    public function upload(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'file_type' => 'required|string',
            'file_size' => 'required|integer',
            'file' => 'required|file|max:10737418240' // 10GB
        ]);

        $accounts = GoogleAccount::where('is_active', 1)->get();
        if (!count($accounts)) {
            Log::error('Không tìm thấy tài khoản drive để upload');
        }

        $file = $request->user()->files()->create([
            'name' => $data['name'],
            'code' => File::generateCode(),
            'file_type' => $data['file_type'],
            'file_size' => $data['file_size']
        ]);

        foreach ($accounts as $account) {
            $api = new GoogleApi($account);
            if ($token = $api->getToken()) {
                if ($uploadId = $api->preUploadFile($token, $data['name'], $data['file_type'])) {
                    if ($fileData = $api->uploadFile($data['name'], $data['file'], $uploadId)) {
                        if (!empty($fileData['id'])) {
                            $file->uploadedFiles()->create([
                                'google_account_id' => $account->id,
                                'google_file_id' => $fileData['id']
                            ]);
                        }
                    } else {
                        Log::error('Không thể upload file tài khoản: ' . $account->name);
                    }
                } else {
                    Log::error('Không thể pre upload file tài khoản: ' . $account->name);
                }
            } else {
                Log::error('Không thể làm mới token tài khoản: ' . $account->name);
            }
        }
            
        return new FileResource($file);
    }

    public function uploadClient(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'file_type' => 'required|string',
            'file_size' => 'required|integer',
            'uploads' => 'array|required|min:1',
            'uploads.*.id' => 'string|required|max:255',
            'uploads.*.account_id' => 'required|integer',
        ]);

        $file = $request->user()->files()->create([
            'name' => $data['name'],
            'code' => File::generateCode(),
            'file_type' => $data['file_type'],
            'file_size' => $data['file_size']
        ]);

        foreach ($data['uploads'] as $uploadData) {
            $account = GoogleAccount::findOrFail($uploadData['account_id']);
            $file->uploadedFiles()->create([
                'google_account_id' => $account->id,
                'google_file_id' => $uploadData['id']
            ]);
        }
            
        return new FileResource($file);
    }

    public function deleteFile(Request $request, $fileId)
    {
        $file = File::where('user_id', $request->user()->id)->findOrFail($fileId);
        $file->delete();

        return redirect()->back();
    }
}
