<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'vtiger_invoice';

    protected $fillable = [
        'invoiceid',
        'subject', // Subject
        'contract_number', // Contract Number
        'salesorderid', // Sales Order
        'customerno', // Customer No
        'contactid', // Customer Name
        'invoicedate', // Invoice Date
        'duedate', // Due Date
        'purchaseorder', // Purchase Order
        'adjustment', // Adjustment
        'salescommission', // Sales Commission
        'exciseduty', // Excise Duty
        'subtotal', // Sub Total
        'total', // Total
        'taxtype', // Tax Type
        'discount_percent', // Discount Percent
        'discount_amount', // Discount Amount
        's_h_amount', // S&H Amount
        'accountid', // Account Name
        'invoicestatus', // Status
        'currency_id', // Currency
        'conversion_rate', // Conversion Rate
        'terms_conditions', // Terms & Conditions
        'invoice_no', // Invoice No
        'pre_tax_total', // Pre Tax Total
        'received', // Received
        'balance', // Balance
        's_h_percent', // S&H Percent
        'potential_id', // Potential Name
        'tags', // Tags
        'region_id', // Tax Region
    ];

    public function documents()
    {
        return $this->hasManyThrough(Document::class, DocumentRel::class, 'crmid', 'notesid', 'invoiceid', 'notesid');
    }

    public function invoiceCF()
    {
        return $this->hasOne(InvoiceCF::class, 'invoiceid', 'invoiceid');
    }

    public function paymentPlanCF()
    {
        return $this->hasOne(PaymentPlanCF::class, 'cf_1773', 'invoiceid');
    }

    public function paymentPlan()
    {
        return $this->hasOneThrough(
            \App\Models\PaymentPlan::class,
            \App\Models\PaymentPlanCF::class,
            'cf_1773',         // Foreign key on PaymentPlanCF (represents invoiceid)
            'paymentplansid',  // Foreign key on PaymentPlan
            'invoiceid',       // Local key on Invoice
            'paymentplansid'          // Local key on PaymentPlanCF (represents paymentplansid)
        );
    }

    public function contact()
    {
        return $this->belongsTo(ContactDetails::class, 'contactid', 'contactid');
    }

    public function contactCF()
    {
        return $this->belongsTo(ContactCF::class, 'contactid', 'contactid');
    }

    public function crmEntity()
    {
        return $this->belongsTo(CRMEntity::class, 'invoiceid', 'crmid');
    }
}
