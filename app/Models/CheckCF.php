<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckCF extends Model
{
    use HasFactory;

    protected $table = 'vtiger_checkscf';

    protected $fillable = [
        'checksid',
        'cf_1697', // Unit Number
        'cf_1699', // Customer Employment Name
        'cf_1701', // Contract Number
        'cf_1703', // Collection Date
        'cf_1705', // Currency type
        'cf_1707', // Collection Type
        'cf_1709', // Deposit Bank
        'cf_1711', // Check Number - Transfer
        'cf_1713', // Status
        'cf_1715', // Note
        'cf_1717', // اسم العميل
        'cf_1719', // رقم الوحدة
        'cf_1721', // Amount
        'cf_1723', // Bank Name
        'cf_1755', // Payment Plan
        'cf_1771', // Contract
        'cf_1781', // Type
    ];

    public function check()
    {
        return $this->belongsTo(Check::class, 'checksid', 'checksid');
    }

    public function paymentPlan()
    {
        return $this->belongsTo(PaymentPlan::class, 'cf_1755', 'paymentplansid');
    }

    public function paymentPlanCF()
    {
        return $this->belongsTo(PaymentPlanCF::class, 'cf_1755', 'paymentplansid');
    }

    public function invoiceCF()
    {
        return $this->belongsTo(InvoiceCF::class, 'cf_1781', 'invoiceid');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'cf_1781', 'invoiceid');
    }
}
