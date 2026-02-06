<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSpecificationTable extends Migration
{
    public function up()
    {
        Schema::create('product_specification', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('specification_id');
            $table->text('value')->nullable()->default(null);
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('specification_id')->references('id')->on('specifications')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_specification');
    }
}
