<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Forme;
use Carbon\Carbon;
use App\Models\Titre;
use App\Models\Essence;
use App\Models\Societe;
use Illuminate\Support\Facades\DB; // Ajoutez cette ligne
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
        'conditionnemment_id',
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
        return $this->getFormatedDateTime($value);
    }

    function getFormatedDateTime($date)
    {
        $locale = app()->getLocale();
        Carbon::setLocale($locale);
        $format = $locale === 'en' ? 'F d, Y' : 'd M Y';

        return Carbon::parse($date)->translatedFormat($format);
    }

    /**
     * Les événements de modèle.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($transaction) {
            // Vérifier si la transaction peut être supprimée
            if (!$transaction->canBeDeleted()) {
                throw new \Exception('Cette transaction ne peut pas être supprimée.');
            }
        });
    }

    /**
     * Vérifie si la transaction peut être supprimée
     */
    public function canBeDeleted(): bool
    {
        // Vérifier si la transaction a des dépendances
        return !$this->hasRelatedRecords();
    }

    /**
     * Vérifie si la transaction a des enregistrements liés
     */
    private function hasRelatedRecords(): bool
    {
        // Ajouter ici d'autres vérifications si nécessaire
        return false;
    }

    /**
     * Supprime la transaction de manière sécurisée
     */
    public function safeDelete(): bool
    {
        try {
            DB::beginTransaction();

            // Mettre à jour les volumes si nécessaire
            $this->updateVolumes();

            // Supprimer la transaction
            $deleted = $this->delete();

            DB::commit();
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Met à jour les volumes associés lors de la suppression
     */
    private function updateVolumes(): void
    {
        // Mettre à jour le volume restant du titre si nécessaire
        if ($this->titre_id && $this->volume) {
            $titre = $this->titre;
            $essence = $this->essence;

            if ($titre && $essence) {
                $pivotRecord = $titre->essence()->where('essence_id', $essence->id)->first();
                if ($pivotRecord) {
                    $newVolume = $pivotRecord->pivot->VolumeRestant + $this->volume;
                    $titre->essence()->updateExistingPivot($essence->id, [
                        'VolumeRestant' => $newVolume
                    ]);
                }
            }
        }
    }
}
