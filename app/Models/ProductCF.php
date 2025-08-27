<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCF extends Model
{
    use HasFactory;

    protected $table = 'vtiger_productcf';

    protected $fillable = [
        'productid',
        'cf_1135', // Unit Type
        'cf_1137', // Gross Area
        'cf_1139', // City
        'cf_1141', // Rooms
        'cf_1143', // Garden
        'cf_1145', // Net Area
        'cf_1147', // Finishing
        'cf_1149', // Roof Comment
        'cf_1151', // Extra 1
        'cf_1153', // Extra 1 Price p-sqm
        'cf_1155', // Extra 1 Area
        'cf_1157', // Request
        'cf_1159', // Floor Level
        'cf_1161', // Extra 2
        'cf_1163', // Extra 1 Price
        'cf_1165', // Extra 2 Price p-sqm
        'cf_1167', // Extra 2 Area
        'cf_1169', // Extra 2 Price
        'cf_1171', // Payment Details
        'cf_1173', // المدفوع
        'cf_1175', // حرف القطعة
        'cf_1177', // رقم القطعة
        'cf_1179', // District
        'cf_1181', // الحالة
        'cf_1251', // Project Name
        'cf_1286', // Client Mobile 1
        'cf_1288', // Client Mobile 2
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'productid', 'productid');
    }
}
