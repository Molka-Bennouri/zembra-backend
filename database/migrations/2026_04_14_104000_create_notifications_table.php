<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->enum('type', ['success', 'warning', 'info', 'error'])->default('info');
            $table->boolean('seen')->default(false);
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
