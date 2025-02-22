<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Forme;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    public $fillable = [
        'date',
        'exercice',
        'numero',
        'societe_id',
        'destination',
        'pays',
        'titre_id',
        'essence_id',
        'forme_id',
        'conditionnemment_id',
        'type_id',
        'volume',
    ];
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

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
    public function conditionnnement()
    {
        return $this->belongsTo(Conditionnemment::class);
    }
    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }
    public function titre()
    {
        return $this->belongsTo(Titre::class);
    }
}
