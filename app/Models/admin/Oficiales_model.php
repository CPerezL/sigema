<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oficiales_model extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'sgm_oficiales';
}
