<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->enum('type', ['free_delivery', 'discount', 'banner', 'offers'])
                  ->default('discount')
                  ->after('slug');
            $table->decimal('min_cart_value', 12, 2)
                  ->nullable()
                  ->after('type')
                  ->comment('Only applies to free_delivery campaigns');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['type', 'min_cart_value']);
        });
    }
};
