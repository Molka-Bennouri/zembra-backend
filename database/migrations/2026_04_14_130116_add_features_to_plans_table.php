<?php

// database/migrations/xxxx_add_features_to_plans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->json('features')->nullable();
            $table->boolean('recommended')->default(false);
        });
    }

    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['features', 'recommended']);
        });
    }
};
