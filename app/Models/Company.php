<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User; 

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'company_email',
        'company_code',
        'company_address',
        'services',
        'bio',
        'accreditation_certificate',
        'photo_company',
        'status', 
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function jobs()
    {
        return $this->hasMany(Job::class, 'company_id');
    }
}