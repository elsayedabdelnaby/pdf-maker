<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check extends Model
{
    use HasFactory;

    protected $table = 'vtiger_checks';

    protected $fillable = [
        'checksid',
        'bankname'
    ];

    public function crmEntity()
    {
        return $this->belongsTo(CRMEntity::class, 'checksid', 'crmid');
    }
}
