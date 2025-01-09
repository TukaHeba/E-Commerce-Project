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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_male')->nullable();     //1=male , 0=female
            $table->date('birthdate')->nullable();
            $table->string('telegram_user_id')->nullable()->unique();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // Indexing columns to optimize performance
            $table->unique('phone', 'index_users_phone');
            $table->fullText('address', 'index_users_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
