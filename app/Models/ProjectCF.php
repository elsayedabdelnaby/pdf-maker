<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCF extends Model
{
    use HasFactory;

    protected $table = 'vtiger_projectcf';

    protected $fillable = [
        'projectid',
        'cf_1327', // Developer History
        'cf_1329', // Location
        'cf_1331', // District
        'cf_1333', // Project Usage
        'cf_1335', // Facilities and Selling Points
        'cf_1337', // Address
        'cf_1339', // City
        'cf_1341', // Delivery
        'cf_1343', // Facilities
        'cf_1345', // Finishing
        'cf_1347', // Unit Type
        'cf_1349', // Price
        'cf_1351', // Developer Name
        'cf_1353', // Previous Projects
        'cf_1355', // Land Area
        'cf_1357', // Product Types
        'cf_1359', // Payment Plans
        'cf_1361', // Club and Parking
        'cf_1363', // Facilities.
        'cf_1365', // Price Details
        'cf_1367', // Maintenance
        'cf_1371', // Finishing Specs
        'cf_1373', // Delivery Date
        'cf_1461', // Display in Home Page
        'cf_1463', // Display in the Website
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projectid', 'projectid');
    }
}
