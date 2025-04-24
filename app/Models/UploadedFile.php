<?php

namespace App\Models;

use App\Models\GoogleAccount;
use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    protected $fillable = [
        'google_account_id', 'file_id', 'google_file_id', 'total_download'
    ];

    public function account()
    {
        return $this->belongsTo(GoogleAccount::class, 'google_account_id');
    }
}
