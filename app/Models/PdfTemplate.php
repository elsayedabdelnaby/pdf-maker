<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PdfTemplate extends Model
{
    protected $fillable = [
        'name',
        'engine',
        'page_size',
        'orientation',
        'margin_top',
        'margin_right',
        'margin_bottom',
        'margin_left',
        'rtl',
        'fonts',
        'css',
        'header_html',
        'footer_html',
        'body_html'
    ];

    protected $casts = [
        'fonts' => 'array',
        'rtl' => 'boolean',
    ];
}
