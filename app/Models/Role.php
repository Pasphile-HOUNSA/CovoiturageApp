<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'role';
    protected $primaryKey = 'idRole';

    protected $fillable = [
        'content',
        'slug'
    ];

    // Relations
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role', 'idRole', 'idUser');
    }
}
