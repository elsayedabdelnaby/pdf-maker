<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'vtiger_project';

    protected $fillable = [
        'projectid',
        'projectname',
        'project_no',
        'startdate',
        'targetenddate',
        'actualenddate',
        'targetbudget',
        'projecturl',
        'projectstatus',
        'projectpriority',
        'projecttype',
        'progress',
        'linktoaccountscontacts',
        'tags',
        'isconvertedfrompotential',
        'potentialid'
    ];

    public function projectCF()
    {
        return $this->hasOne(ProjectCF::class, 'projectid', 'projectid');
    }

    public function documents()
    {
        return $this->hasManyThrough(Document::class, DocumentRel::class, 'crmid', 'notesid', 'projectid', 'notesid');
    }
}
