<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cdr extends Model
{
    // for Carbon
    protected $dates = ['created_at', 'updated_at', 'start_datetime', 'end_datetime'];
}
