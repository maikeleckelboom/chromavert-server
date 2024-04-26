<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auth_providers', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('provider_user_name')->nullable();
            $table->string('provider_user_nickname')->nullable();
            $table->string('provider_user_email')->nullable();
            $table->string('provider_user_avatar')->nullable();
            $table->string('provider_user_id');
            $table->unique(['provider', 'provider_user_id']);
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth_providers');
    }
};
