<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRel extends Model
{
    use HasFactory;

    protected $table = 'vtiger_senotesrel';

    protected $fillable = [
        'crmid',
        'notesid',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'notesid', 'notesid');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'crmid', 'projectid');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'crmid', 'productid');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'crmid', 'invoiceid');
    }
}
