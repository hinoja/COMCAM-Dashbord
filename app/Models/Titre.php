<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Zone;
use App\Models\Forme;
use App\Models\Essence;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Titre extends Model
{
    public $fillable = ['exercice', 'nom', 'localisation', 'zone_id'];
    // , 'volume','VolumeRestant'
    /** @use HasFactory<\Database\Factories\TitreFactory> */
    use HasFactory;
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function essence()
    {
        // return $this->belongsToMany(Essence::class,'titre_id','essence_id');
        return $this->belongsToMany(Essence::class)
        ->withPivot('volume', 'VolumeRestant')
        ->withTimestamps();
    }

    // public function forme()
    // {
    //     return $this->belongsTo(Forme::class);
    // }
    // public function type()
    // {
    //     return $this->belongsTo(Type::class);
    // }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function delete()
    {
        // Supprimer d'abord les relations dans la table pivot
        $this->essence()->detach();

        // Ensuite supprimer le titre
        return parent::delete();
    }
}
