<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;
    protected $fillable = ['day', 'month', 'year', 'week_day'];

    public function month(){
        return $this->belongsTo(Month::class);
    }
}
