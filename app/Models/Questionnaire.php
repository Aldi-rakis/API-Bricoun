<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    protected $fillable = ['question'];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
