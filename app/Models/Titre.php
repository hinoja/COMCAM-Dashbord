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
    public $fillable = ['exercice', 'nom', 'localisation', 'zone_id', 'essence_id', 'forme_id', 'type_id', 'volume','VolumeRestant'];
    /** @use HasFactory<\Database\Factories\TitreFactory> */
    use HasFactory;
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function essence()
    {
        return $this->belongsTo(Essence::class);
    }
    public function forme()
    {
        return $this->belongsTo(Forme::class);
    }
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
