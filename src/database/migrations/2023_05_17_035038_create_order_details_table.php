<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->references('id')->on('orders');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('first_kana');
            $table->string('last_kana');
            $table->string('phone')->nullable();
            $table->string('billing_address1', 255);
            $table->string('billing_address2', 255)->nullable();
            $table->string('billing_city', 255);
            $table->string('billing_state', 45)->nullable();
            $table->string('billing_zipcode', 45);
            $table->string('billing_country_code', 3);
            $table->string('shipping_address1', 255);
            $table->string('shipping_address2', 255)->nullable();
            $table->string('shipping_city', 255);
            $table->string('shipping_state', 45)->nullable();
            $table->string('shipping_zipcode', 45);
            $table->string('shipping_country_code', 3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
};
