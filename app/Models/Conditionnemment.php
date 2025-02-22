<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conditionnemment extends Model
{
    /** @use HasFactory<\Database\Factories\ConditionnemmentFactory> */
    use HasFactory;

    protected $fillable = [
        'designation',
        'code',
    ];
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
