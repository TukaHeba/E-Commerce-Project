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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->string('postal_code');
            $table->enum('status', ['pending', 'shipped', 'delivered', 'canceled'])->default('pending');
            $table->decimal('total_price', 10, 2);
            $table->string('order_number')->unique();
            $table->timestamps();
            $table->softDeletes();

            // Indexing columns to optimize performance
            $table->index('zone_id', 'index_orders_zone');
            $table->index('status', 'index_orders_status');
            $table->index('total_price', 'index_orders_total_price');
            $table->index('created_at', 'index_orders_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
