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
        // database/migrations/xxxx_xx_xx_create_hosting_plans_table.php

        Schema::create('hosting_plans', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_price_id')->unique();
            $table->string('name');
            $table->string('interval'); // 'monthly' or 'yearly'
            $table->integer('amount'); // price in cents
            $table->string('currency', 10)->default('usd');
            $table->text('description')->nullable(); // editable by admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_plans');
    }
};
