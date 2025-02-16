<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class PassportApplication extends Model
{
    use HasUlids;

    protected $fillable = [
        "workflow_id",
        "created_by"
    ];
}
