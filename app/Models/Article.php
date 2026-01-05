<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = "Article";
    protected $primaryKey = "idArticle";
    public $timestamps = false;
        public $incrementing = true;
    protected $keyType = 'int';
}
