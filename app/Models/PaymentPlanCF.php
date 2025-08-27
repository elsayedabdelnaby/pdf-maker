<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentPlanCF extends Model
{
    use HasFactory;

    protected $table = 'vtiger_paymentplanscf';

    protected $fillable = [
        'paymentplansid',
        'cf_1731', // Payment Options
        'cf_1733', // Down Payment
        'cf_1735', // Payment Method
        'cf_1739', // Reset
        'cf_1741', // Unit Area
        'cf_1743', // Meter Unit Price
        'cf_1745', // Garden Area
        'cf_1747', // Garden Meter Price
        'cf_1749', // Unit Price
        'cf_1757', // Customer Employment Name
        'cf_1759', // Bank Name
        'cf_1761', // Quarterly
        'cf_1763', // Half Yearly
        'cf_1765', // Annual
        'cf_1767', // Handover Payment
        'cf_1769', // Year of Handover Payment
        'cf_1773', // Contract
        'cf_1775', // Maintenance Fee
        'cf_1777', // Maintenance Fee Value
        'cf_1779', // Maintenance Fee Collection Year
        'cf_1783', // Handover Payment Value
        'cf_1785', // 1st Installment Date
        'cf_1787', // Down Payment %
    ];

    public function paymentplan()
    {
        return $this->belongsTo(PaymentPlan::class, 'paymentplansid', 'paymentplansid');
    }

    public function invoiceCF()
    {
        return $this->belongsTo(InvoiceCF::class, 'cf_1773', 'invoiceid');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'cf_1773', 'invoiceid');
    }
}
