<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maincategory_subcategory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('main_category_id')->constrained('main_categories')->cascadeOnDelete();
            $table->foreignId('sub_category_id')->constrained('sub_categories')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexing columns to optimize performance
            $table->unique(['main_category_id', 'sub_category_id'], 'unique_main_sub_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maincategory_subcategory');
    }
};
