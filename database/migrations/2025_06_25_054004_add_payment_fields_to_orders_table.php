<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_type');
            $table->timestamp('paid_at')->nullable()->after('payment_reference');
        });

        // Update enum for payment_type to include 'qr'
        DB::statement("ALTER TABLE orders MODIFY payment_type ENUM('cash', 'card', 'wallet', 'mixed','others','online') DEFAULT 'cash'");
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'paid_at']);
        });

        // Rollback enum change (adjust according to previous state)
        DB::statement("ALTER TABLE orders MODIFY payment_type ENUM('cash', 'card', 'wallet','mixed','others','online') DEFAULT 'cash'");
    }
};
