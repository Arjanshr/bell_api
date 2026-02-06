<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFulltextIndexesToSearchableColumns extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->fullText(['name', 'description','keywords']);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->fullText(['name', 'description']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->fullText(['name', 'description']);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropFullText(['name', 'description','keywords']);
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->dropFullText(['name', 'description']);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropFullText(['name', 'description']);
        });
    }
}
