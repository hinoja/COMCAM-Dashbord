<?php

namespace App\Models;

use App\Models\Forme;
use App\Models\Titre;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    /** @use HasFactory<\Database\Factories\TypeFactory> */
    use HasFactory;
    public $fillable = ['designation', 'code', 'forme_id'];
    public function forme()
    {
        return $this->belongsTo(Forme::class);
    }
    public function titres()
    {
        return $this->hasMany(Titre::class);
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
