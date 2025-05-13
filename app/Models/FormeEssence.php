<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormeEssence extends Model
{
    use HasFactory;

    protected $table = 'forme_essences';

    protected $fillable = ['forme_id', 'type_id', 'essence_id'];

    public function forme()
    {
        return $this->belongsTo(Forme::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function essence()
    {
        return $this->belongsTo(Essence::class);
    }
}
