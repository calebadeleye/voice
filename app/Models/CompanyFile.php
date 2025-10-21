<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyFile extends Model
{

    protected $fillable = [
        'company_id',
        'file_name',
        'file_path',
        'file_type',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
}
