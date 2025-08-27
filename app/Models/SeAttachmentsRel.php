<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeAttachmentsRel extends Model
{
    use HasFactory;

    protected $table = 'vtiger_seattachmentsrel';

    protected $fillable = [
        'crmid',
        'attachmentsid'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'crmid', 'notesid');
    }

    public function attachment()
    {
        return $this->belongsTo(Attachment::class, 'attachmentsid', 'attachmentsid');
    }
} 