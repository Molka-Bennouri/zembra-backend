<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('query_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->enum('type', ['listing', 'reviews']);
            $table->string('network');
            $table->string('slug');
            $table->json('fields')->nullable();
            $table->json('filters')->nullable(); // min_rating, max_rating, date_range (reviews only)
            $table->enum('status', ['success', 'error']);
            $table->json('response')->nullable();
            $table->timestamp('executed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('query_histories');
    }
};
