<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceCF extends Model
{
    use HasFactory;

    protected $table = 'vtiger_invoicecf';

    protected $fillable = [
        'productid',
        'cf_1183', // Check Date
        'cf_1185', // Unit Type
        'cf_1187', // Tax Start Date
        'cf_1189', // Next Date
        'cf_1191', // Contract Date
        'cf_1193', // Partial invoice
        'cf_1195', // Payment Plan
        'cf_1197', // تعديل الباقى
        'cf_1199', // Currency type
        'cf_1201', // Next Follow Details
        'cf_1203', // Developer Name
        'cf_1207', // Unit No
        'cf_1209', // Company%
        'cf_1211', // اجمالى ثمن الوحدة
        'cf_1213', // Unit Area
        'cf_1215', // No. of Floors
        'cf_1217', // Incentive%
        'cf_1219', // Unit Number
        'cf_1221', // المقدم
        'cf_1223', // Incentive Caim Date
        'cf_1225', // Incentive % Man
        'cf_1227', // Manager ID
        'cf_1229', // Manager Name
        'cf_1231', // Client Name
        'cf_1233', // 8 years
        'cf_1235', // Incentive Man Caim Date
        'cf_1237', // 10 Years
        'cf_1239', // 16 yrs 20%
        'cf_1241', // 16 yrs 10%
        'cf_1243', // 16 yrs 15%
        'cf_1245', // 12 Years
        'cf_1247', // Net value
        'cf_1249', // شهر التسليم
        'cf_1379', // 88%
        'cf_1537', // Garden
        'cf_1539', // Building No.
        'cf_1541', // ثمن الوحدة بالحروف
        'cf_1543', // الباقى
        'cf_1545', // الباقى و قدره
        'cf_1547', // دفعة الحجز
        'cf_1549', // دفعة الحجز وقدره
        'cf_1551', // انه فى يوم
        'cf_1553', // ﻣﻦ ﺷﻬﺮ
        'cf_1555', // مبلغ صيانة
        'cf_1557', // ﻣﺒﻠﻎ ﺻﻴﺎﻧﻪ وقدره
        'cf_1559', // مبلغ 3
        'cf_1561', // مبلغ 3 وقدره
        'cf_1563', // مبلغ 4
        'cf_1565', // مبلغ 4 وقدره
        'cf_1567', // Unit Area بالحروف
        'cf_1569', // سنة التسليم
        'cf_1573', // يوم رقم
        'cf_1575', // تعديل المقدم
        'cf_1591', // اضافة مساحة الجاردن فى العقد
        'cf_1629', // Confirm
        'cf_1631', // Confirm Comment
        'cf_1669', // Project Name
        'cf_1671', // Project Category
        'cf_1673', // Project Location
        'cf_1675', // Unit Category
        'cf_1677', // Contract Status
        'cf_1789', // Payment Plan Module
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceid', 'invoiceid');
    }

    public function paymentPlanCF()
    {
        return $this->hasOne(PaymentPlanCF::class, 'cf_1789', 'paymentplansid');
    }

    public function paymentPlan()
    {
        return $this->hasOne(PaymentPlan::class, 'cf_1789', 'paymentplansid');
    }

    public function crmEntity()
    {
        return $this->belongsTo(CRMEntity::class, 'invoiceid', 'crmid');
    }
}
