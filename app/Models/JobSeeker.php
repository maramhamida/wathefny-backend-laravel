<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobSeeker extends Model
{
    protected $fillable = [
        'user_id',
        'id_number',
        'major',
        'experience_area',
        'about_me',
        'certificate',
        'photo',
    ];
    public function user()
{
    return $this->belongsTo(User::class);
}
}
