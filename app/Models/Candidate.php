<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_id',
        'name',
        'email',
        'phone',
        'year',
        'cretated_by',
        'updated_by',
        'deleted_by'
    ];
    public function job()
    {
        return $this->hasOne(Job::class);
    }
}
