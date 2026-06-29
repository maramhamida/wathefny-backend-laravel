<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

  
    protected $table = 'job_posts';

    protected $fillable = [
        'company_id',
        'title',
        'location',
        'salary',
        'employment_type',
        'description',
        'status', 
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

public function savedJobs()
{
   
    return $this->belongsToMany(Job::class, 'saved_jobs', 'user_id', 'job_id');
}
}