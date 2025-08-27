<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePdfTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pdf_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('engine')->default('handlebars'); // handlebars|mustache
            $table->string('page_size')->default('A4');
            $table->enum('orientation', ['portrait', 'landscape'])->default('portrait');
            $table->integer('margin_top')->default(20);
            $table->integer('margin_right')->default(15);
            $table->integer('margin_bottom')->default(20);
            $table->integer('margin_left')->default(15);
            $table->boolean('rtl')->default(false);
            $table->json('fonts')->nullable();
            $table->longText('css')->nullable();
            $table->longText('header_html')->nullable();
            $table->longText('footer_html')->nullable();
            $table->longText('body_html'); // main template
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pdf_templates');
    }
}
