<?php

namespace App\Models;

use App\Models\Titre;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    /** @use HasFactory<\Database\Factories\ZoneFactory> */
    use HasFactory;
    public $fillable = ['name'];
    public function titres()
    {
        return $this->hasMany(Titre::class);
    }
}
