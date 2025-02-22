<?php

namespace App\Models;

use App\Models\Titre;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Societe extends Model
{
    protected $fillable = ['acronym'];
    /** @use HasFactory<\Database\Factories\SocieteFactory> */
    use HasFactory;
    public function titres()
    {
        return $this->hasMany(Titre::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
