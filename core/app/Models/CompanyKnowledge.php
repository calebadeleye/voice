<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyKnowledge extends Model
{

    protected $fillable = [
        'company_id',
        'company_file_id',
        'content',
        'embedding',
    ];

    protected $casts = [
        'embedding' => 'array',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function file()
    {
        return $this->belongsTo(CompanyFile::class, 'company_file_id');
    }
}
