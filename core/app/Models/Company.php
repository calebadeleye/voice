<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Company extends Model
{
    
    protected $fillable = [
    'user_id',
    'name',
    'industry',
    'description',
    'website',
    'phone',
    'email',
    'ai_name',
    'africastalking_number',
    'openai_api_key',
    'assistant_description',
];

    public function files()
    {
        return $this->hasMany(CompanyFile::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

}
