<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCF extends Model
{
    use HasFactory;

    protected $table = 'vtiger_contactscf';

    protected $fillable = [
        'contactid',
        'cf_1086', // L Source
        'cf_1282', // National ID/Passport
        'cf_1284', // Type
        'cf_1393', // Work field
        'cf_1395', // Amount paid
        'cf_1679', // Name in Arabic
        'cf_1681', // Customer First Degree Name
        'cf_1683', // Customer First Degree relation
        'cf_1685', // Customer First Degree Cellphone
        'cf_1687', // Customer First Degree Email
        'cf_1689', // Customer Employment Name
    ];

    public function contact()
    {
        return $this->belongsTo(ContactDetails::class, 'contactid', 'contactid');
    }

    public function crmEntity()
    {
        return $this->belongsTo(CRMEntity::class, 'contactid', 'crmid');
    }

}
