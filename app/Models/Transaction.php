<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Forme;
use Carbon\Carbon;
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
        // 'forme_id',
        'conditionnemment_id',
        // 'type_id',
        'volume',
    ];
    /** @use HasFactory<\Database\Factories\TransactionFactory> */
    use HasFactory;

    public function essence()
    {
        return $this->belongsTo(Essence::class);
    }
    /**
     * Récupère la forme via la relation essence->formeEssence
     */
    public function forme()
    {
        return $this->hasOneThrough(
            Forme::class,
            FormeEssence::class,
            'essence_id', // Clé étrangère sur forme_essences
            'id', // Clé primaire sur formes
            'essence_id', // Clé locale sur transactions
            'forme_id' // Clé locale sur forme_essences
        );
    }

    public function type()
    {
        return $this->hasOneThrough(
            Type::class,
            FormeEssence::class,
            'essence_id', // Clé étrangère sur forme_essences
            'id', // Clé primaire sur types
            'essence_id', // Clé locale sur transactions
            'type_id' // Clé locale sur forme_essences
        );
    }
    public function conditionnemment()
    {
        return $this->belongsTo(Conditionnemment::class, 'conditionnemment_id');
    }

    // Alias pour la relation conditionnemment
    public function conditionnnement()
    {
        return $this->conditionnemment();
    }
    public function societe()
    {
        return $this->belongsTo(Societe::class);
    }
    public function titre()
    {
        return $this->belongsTo(Titre::class);
    }
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'datetime',
    ];

    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    function getFormatedDateTime($date)
    {
        $locale = app()->getLocale();
        Carbon::setLocale($locale);
        $format = $locale === 'en' ? 'F d, Y' : 'd M Y';

        return Carbon::parse($date)->translatedFormat($format);
    }
}
