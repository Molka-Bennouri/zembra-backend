<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete(); // ← 'clients' → 'users'
            $table->string('stripe_payment_intent_id')->unique()->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('invoice_pdf_url')->nullable();
            $table->float('amount');
            $table->string('status'); // succeeded | pending | failed
            $table->dateTime('paid_at')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
