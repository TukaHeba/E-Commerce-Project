<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);    // 10 is the total number of digits, and 2 is the number of decimal places.
            $table->unsignedInteger('product_quantity'); // make sure in rules that this field must be positive number , negative numbers not allowed
            $table->foreignId('maincategory_subcategory_id')->constrained('maincategory_subcategory')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // Indexing columns to optimize performance
            $table->fullText('name', 'index_products_name');
            $table->index('price', 'index_products_price');
            $table->index('product_quantity', 'index_products_product_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
