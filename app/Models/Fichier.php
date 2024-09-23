<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fichier extends Model
{
    use HasFactory;


    protected $fillable = ['file_name', 'file_path', 'groupe_id'];

    protected $table = 'fichiers'; // Nom de la table

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }
}
