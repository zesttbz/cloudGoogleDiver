<?php

namespace App\Models;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;

class GoogleAccount extends Model
{
    protected $fillable = [
        'name', 'email', 'image', 'refresh_token', 'is_active'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'uploaded_files', 'google_account_id', 'file_id')->withPivot('google_file_id');
    }
}
