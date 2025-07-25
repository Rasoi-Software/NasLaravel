<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('hosting_plans', function (Blueprint $table) {
            $table->string('stripe_product_id')->nullable()->after('stripe_price_id');
        });
    }

    public function down()
    {
        Schema::table('hosting_plans', function (Blueprint $table) {
            $table->dropColumn('stripe_product_id');
        });
    }
};
