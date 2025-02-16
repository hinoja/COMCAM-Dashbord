<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titre extends Model
{
    public $fillable=['exercice','nom','localisation','zone_id','essence_id','forme_id','type_id','volume'];
    /** @use HasFactory<\Database\Factories\TitreFactory> */
    use HasFactory;
}
