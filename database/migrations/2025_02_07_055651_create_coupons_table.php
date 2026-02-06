<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percentage']);
            $table->decimal('discount', 8, 2);
            $table->integer('max_uses')->nullable()->default(null);
            $table->integer('uses')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_user_specific')->default(false);
            $table->enum('specific_type', ['normal', 'category', 'brand', 'product', 'free_delivery'])->nullable();
            $table->integer('status')->default(1); // Default value '1' for Active status
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
        Schema::dropIfExists('coupons');
    }
}
