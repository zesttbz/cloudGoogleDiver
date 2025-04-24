<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Support\Str;
use App\Models\UploadedFile;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'user_id', 'name', 'code', 'google_id', 'file_type', 'file_size', 'total_download'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function uploadedFiles()
    {
        return $this->hasMany(UploadedFile::class, 'file_id');
    }

    public function accounts()
    {
        return $this->belongsToMany(GoogleAccount::class, 'uploaded_files', 'file_id', 'google_account_id')->withPivot('google_file_id');
    }

    public static function generateCode()
    {
        $code = strtoupper(Str::random(15));
        if (self::where('code', $code)->exists()) {
            return self::generateCode();
        }

        return $code;
    }
}
