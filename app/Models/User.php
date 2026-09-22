<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements PasskeyUser
{
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable, SoftDeletes;

    protected $table = 'users';

    protected $primaryKey = 'idUser';

    protected $fillable = [
        'firstName',
        'lastName',
        'sex',
        'birthday',
        'email',
        'password',
        'phoneNumber',
        'address',
        'photoId',
        'numPiece',
        'scanPiece',
        'registrationDate',
        'lastLogin',
        'isActive',
    ];

    protected $hidden = ['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
        'birthday' => 'date',
        'registrationDate' => 'datetime',
        'lastLogin' => 'datetime',
        'isActive' => 'boolean',
    ];

    protected $appends = ['name'];

    public function getNameAttribute(): string
    {
        return trim("{$this->firstName} {$this->lastName}") ?: 'Utilisateur';
    }

    public function getRouteKeyName()
    {
        return 'firstName';
    }

    public function cars()
    {
        return $this->hasMany(Car::class, 'idUser', 'idUser');
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'idUser', 'idUser');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'idUser', 'idUser');
    }

    public function document()
    {
        return $this->hasOne(Document::class, 'idUser', 'idUser');
    }

    public function getProfilePhotoAttribute()
    {
        return $this->photoId ?? null;
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'idUser', 'idUser');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role', 'idUser', 'idRole');
    }

    public function opinionsGiven()
    {
        return $this->hasMany(Opinion::class, 'idEvaluateur', 'idUser');
    }

    public function opinionsReceived()
    {
        return $this->hasMany(Opinion::class, 'idEvalue', 'idUser');
    }
}
