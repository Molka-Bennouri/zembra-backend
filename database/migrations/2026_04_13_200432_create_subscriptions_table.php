<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete(); // ← 'clients' → 'users'
            $table->foreignId('plan_id')->constrained('plans')->restrictOnDelete();
            $table->string('stripe_subscription_id')->unique()->nullable();
            $table->string('status'); // active | canceled | past_due | incomplete
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
