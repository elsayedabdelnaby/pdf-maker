<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'vtiger_products';

    protected $fillable = [
        'productid',
        'product_no',
        'productname',
        'productcode',
        'productcategory',
        'manufacturer',
        'qty_per_unit',
        'unit_price',
        'weight',
        'pack_size',
        'sales_start_date',
        'sales_end_date',
        'start_date',
        'expiry_date',
        'cost_factor',
        'commissionrate',
        'commissionmethod',
        'discontinued',
        'usageunit',
        'reorderlevel',
        'website',
        'taxclass',
        'mfr_part_no',
        'vendor_part_no',
        'serialno',
        'qtyinstock',
        'productsheet',
        'qtyindemand',
        'glacct',
        'vendor_id',
        'imagename',
        'currency_id',
        'is_subproducts_viewable',
        'purchase_cost',
        'tags'
    ];

    public function productCF()
    {
        return $this->hasOne(ProductCF::class, 'productid', 'productid');
    }

    public function documents()
    {
        return $this->hasManyThrough(Document::class, DocumentRel::class, 'crmid', 'notesid', 'productid', 'notesid');
    }
}
