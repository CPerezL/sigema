<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_old extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $table = 'sgm_users';
    use HasFactory;
}
