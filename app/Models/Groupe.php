<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    protected $table = 'groupes'; // Nom de la table


    public function membres()
    {
        return $this->hasMany(Membre::class);
    }

    public function fichiers()
    {
        return $this->hasMany(Fichier::class);
    }
}
