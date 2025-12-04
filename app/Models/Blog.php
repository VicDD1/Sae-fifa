<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $table = "Blog "
    protected $primaryKey = "idBlog "
    public $timestamps = false;
}
