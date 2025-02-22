<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Titre;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Forme extends Model
{
    /** @use HasFactory<\Database\Factories\FormeFactory> */
    use HasFactory;
    public $fillable = ['designation'];
    public function titres()
    {
        return $this->hasMany(Titre::class);
    }
    public function types()
    {
        return $this->hasMany(Type::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
