<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Membre extends Model
{
    use HasFactory, Notifiable;


    protected $fillable = ['name', 'groupe_id', 'email'];

    protected $table = 'membres'; // Nom de la table

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }
}
