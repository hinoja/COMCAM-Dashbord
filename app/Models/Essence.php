<?php

namespace App\Models;

use App\Models\Titre;
use App\Models\Transaction;
use App\Models\Forme;
use App\Models\FormeEssence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Essence extends Model
{
    /** @use HasFactory<\Database\Factories\EssenceFactory> */
    use HasFactory;
    public $fillable = ['nom_local', 'code'];
    public function titres()
    {
        return $this->belongsToMany(Titre::class)
            ->withPivot('volume', 'VolumeRestant')
            ->withTimestamps();
    }
    public function formes()
    {
        return $this->belongsToMany(Forme::class, 'essence_forme', 'essence_id', 'forme_id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    //  public function forme()
    // {
    //     return $this->belongsTo(Forme::class);
    // }

    public function formeEssence()
    {
        return $this->hasOne(FormeEssence::class);
    }

}
