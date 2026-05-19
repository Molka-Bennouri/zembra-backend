<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->enum('context', ['review', 'listing']);

            $table->unique(['name', 'context']); // ← unique par combinaison name+context
        });

        Schema::dropIfExists('review_fields');
        Schema::dropIfExists('response_fields');
    }

    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
