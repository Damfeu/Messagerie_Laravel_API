<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitedMembers extends Model
{
    use HasFactory;

    protected $table = 'invited_members';

    protected $fillable = [ 'email', 'groupe_id'];

    public function group()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');

    }


}
