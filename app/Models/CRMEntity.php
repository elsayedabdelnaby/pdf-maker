<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CRMEntity extends Model
{
    use HasFactory;

    protected $table = 'vtiger_crmentity';

    protected $fillable = [
        'crmid',
        'smcreatorid',
        'smownerid',
        'modifiedby',
        'setype',
        'description',
        'createdtime',
        'modifiedtime',
        'viewedtime',
        'status',
        'version',
        'presence',
        'deleted',
        'smgroupid',
        'source',
        'label'
    ];
}
