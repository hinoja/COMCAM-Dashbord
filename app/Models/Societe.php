<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Societe extends Model
{
    protected $fillable = ['acronym']; 
    /** @use HasFactory<\Database\Factories\SocieteFactory> */
    use HasFactory;
}
