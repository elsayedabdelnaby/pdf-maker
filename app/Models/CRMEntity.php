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

    public function invoice()
    {
        return $this->hasOne(Invoice::class, 'invoiceid', 'crmid');
    }

    public function contact()
    {
        return $this->hasOne(ContactDetails::class, 'contactid', 'crmid');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'productid', 'crmid');
    }

    public function check()
    {
        return $this->hasOne(Check::class, 'checksid', 'crmid');
    }

    public function document()
    {
        return $this->hasOne(Document::class, 'notesid', 'crmid');
    }

    public function paymentPlan()
    {
        return $this->hasOne(PaymentPlan::class, 'paymentplansid', 'crmid');
    }
}
