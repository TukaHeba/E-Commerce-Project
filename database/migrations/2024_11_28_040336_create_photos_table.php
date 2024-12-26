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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('photo_name');
            $table->string('photo_path');
            $table->string('mime_type');
            $table->morphs('photoable'); 
            $table->timestamps();
            $table->softDeletes();
            // Adding indexes to improve query performance for polymorphic relationships
            $table->index(['photoable_id', 'photoable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
