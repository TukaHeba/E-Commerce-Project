<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('rating'); // rating is between 1 to 5
            $table->text('review')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexing columns to optimize performance
            $table->unique(['user_id', 'product_id']);
            $table->index('rating', 'index_rates_rating');
        });
        DB::statement('ALTER TABLE rates ADD CONSTRAINT check_rating_range CHECK (rating >= 1 AND rating <= 5)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
