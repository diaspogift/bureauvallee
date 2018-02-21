<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = ['user_id', 'article_id', 'action', 'created_at', 'updated_at'];
}
