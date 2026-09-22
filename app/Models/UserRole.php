<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_role';
    
    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'idUser',
        'idRole'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser', 'idUser');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'idRole', 'idRole');
    }
}
