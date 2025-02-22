<?php

namespace App\Models;

use App\Models\Titre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Essence extends Model
{
    /** @use HasFactory<\Database\Factories\EssenceFactory> */
    use HasFactory;
    public $fillable = ['nom_local', 'code'];
    public function titres()
    {
        return $this->hasMany(Titre::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
