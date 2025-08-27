<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'vtiger_organizationdetails';

    protected $fillable = [
        'organization_id',
        'organizationname',
        'address',
        'city',
        'state',
        'country',
        'code',
        'phone',
        'fax',
        'website',
        'logoname',
        'logo',
        'vatid'
    ];
}
