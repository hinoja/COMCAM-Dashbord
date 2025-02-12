<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conditionnemment extends Model
{
    /** @use HasFactory<\Database\Factories\ConditionnemmentFactory> */
    use HasFactory;

    protected $fillable = [
        'designation',
        'code',
    ];

}
