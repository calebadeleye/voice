<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallRecord extends Model
{
    protected $fillable = [
                'user_id',
                'caller',
                'session_id',
                'duration',
                'at_cost',
                'vapi_cost',
                'total_cost',
                'status',
                'recording_url',
            ];
}
