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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            // avoiding using float for monetary values to avoid rounding errors due to the inability to represent certain decimal values precisely in binary format.
            // example $price = 0.1 + 0.2; // result might be 0.30000000000000004 instead of 0.3 because of how computer system stores floating points in binary lanaguge
            $table->decimal('price', 10, 2);    // 10 is the total number of digits, and 2 is the number of decimal places.  
            $table->unsignedInteger('product_quantity'); // make sure in rules that this field must be positive number , negative numbers not allowed
            $table->foreignId('category_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
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
