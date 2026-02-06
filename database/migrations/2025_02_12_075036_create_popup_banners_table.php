<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('popup_banners', function (Blueprint $table) {
            $table->id();
            $table->string('image_url');
            $table->boolean('is_active')->default(true); // Store if the banner is visible or not
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('popup_banners');
    }
};
