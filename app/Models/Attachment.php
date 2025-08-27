<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $table = 'vtiger_attachments';

    protected $fillable = [
        'attachmentsid',
        'name',
        'description',
        'type',
        'path',
        'storedname',
        'subject'
    ];

    public function seAttachmentsRel()
    {
        return $this->hasOne(SeAttachmentsRel::class, 'attachmentsid', 'attachmentsid');
    }

    public function getFullUrlAttribute()
    {
        // Build the full URL for the attachment
        $baseUrl = 'https://zayedar.egyptcrm.com';
        $fullPath = $baseUrl . '/' . $this->path . $this->attachmentsid . '_' . $this->storedname;

        return $fullPath;
    }

    public function getImageUrlAttribute()
    {
        // For images, return the full URL
        if (str_starts_with($this->type, 'image/')) {
            return $this->full_url;
        }

        return null;
    }
}
