<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'vtiger_notes';
    
    protected $fillable = [
        'notesid',
        'note_no',
        'title',
        'filename',
        'notecontent',
        'folderid',
        'filetype',
        'filelocationtype',
        'filedownloadcount',
        'filestatus',
        'filesize',
        'fileversion',
        'tags'
    ];

    public function documentRel()
    {
        return $this->hasOne(DocumentRel::class, 'notesid', 'notesid');
    }

    public function seAttachmentsRel()
    {
        return $this->hasOne(SeAttachmentsRel::class, 'crmid', 'notesid');
    }

    public function attachments()
    {
        return $this->hasManyThrough(Attachment::class, SeAttachmentsRel::class, 'crmid', 'attachmentsid', 'notesid', 'attachmentsid');
    }

}
