<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlan extends Model
{
    use HasFactory;

    protected $table = 'vtiger_paymentplans';

    protected $fillable = [
        'paymentplansid',
        'unit',
    ];

    public function unit()
    {
        return $this->belongsTo(Product::class, 'unit', 'productid');
    }

    public function paymentplanCF()
    {
        return $this->hasOne(PaymentPlanCF::class, 'paymentplansid', 'paymentplansid');
    }

    public function invoiceCF()
    {
        return $this->hasOneThrough(
            \App\Models\InvoiceCF::class,
            \App\Models\PaymentPlanCF::class,
            'paymentplansid', // Foreign key on PaymentPlanCF
            'invoiceid',      // Foreign key on InvoiceCF
            'paymentplansid', // Local key on PaymentPlan
            'cf_1773'         // Local key on PaymentPlanCF (represents invoiceid)
        );
    }

    public function invoice()
    {
        return $this->hasOneThrough(
            \App\Models\Invoice::class,
            \App\Models\PaymentPlanCF::class,
            'paymentplansid', // Foreign key on PaymentPlanCF
            'invoiceid',      // Foreign key on Invoice
            'paymentplansid', // Local key on PaymentPlan
            'cf_1773'         // Local key on PaymentPlanCF (represents invoiceid)
        );
    }

    public function checksCF()
    {
        return $this->hasMany(CheckCF::class, 'cf_1755', 'paymentplansid');
    }
}
