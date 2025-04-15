<?php

namespace App\Models;
 
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;
    public $fillable = ['name'];
    public $timestamps = false;
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
