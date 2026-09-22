<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'car';
    protected $primaryKey = 'idCar';

    protected $fillable = [
        'idUser',
        'type',
        'mark',
        'model',
        'registrationNumber',
        'nbrPlaces',
        'color',
        'carte_grise_numero',
        'carte_grise_scan',
        'assurance_numero',
        'assurance_expiration'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'idUser', 'idUser');
    }

    public function trajets()
    {
        return $this->hasMany(Trajet::class, 'idCar', 'idCar');
    }
}
