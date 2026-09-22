<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'document';
    protected $primaryKey = 'idDocument';

    protected $fillable = [
        'idUser',
        'numPermis',
        'permis_expiration',
        'scanPermis',
        'certificatApt',
        'certificatApt_scan'
    ];

    protected $casts = [
        'permis_expiration' => 'date',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'idUser', 'idUser');
    }
}
