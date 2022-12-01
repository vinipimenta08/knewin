<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JwtPermission extends Model
{
    use HasFactory;
    protected $table = "jwt_permissions";
    
    protected $fillable = [
        'token',
        'local'
    ];
}
