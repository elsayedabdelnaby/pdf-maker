<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactDetails extends Model
{
    use HasFactory;

    protected $table = 'vtiger_contactdetails';

    protected $fillable = [
        'contactid',         // Contact Id
        'salutation',        // Salutation
        'firstname',         // First Name
        'contact_no',        // Contact Id (alternate)
        'phone',             // Office Phone
        'lastname',          // Full Name
        'mobile',            // Cellphone
        'accountid',         // Account Name
        'title',             // Title
        'fax',               // Fax
        'department',        // Department
        'email',             // Customer Email
        'reportsto',         // Reports To
        'secondaryemail',    // Secondary Email
        'donotcall',         // Do Not Call
        'emailoptout',       // Email Opt Out
        'reference',         // Reference
        'notify_owner',      // Notify Owner
        'imagename',         // Contact Image
        'isconvertedfromlead', // Is Converted From Lead
        'tags',              // Tags
        'bankname',          // Bank Name (already present)
    ];

    public function contactCF()
    {
        return $this->hasOne(ContactCF::class, 'contactid', 'contactid');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'contactid', 'contactid');
    }
}
