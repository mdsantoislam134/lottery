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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('remark')->nullable();
            $table->foreignid('user_id')->constrained('users');
            $table->string('username');
            $table->string('workingdate');
            $table->string('companies');
            $table->string('lotterycode');
            $table->string('betcount');
            $table->string('totalamount');
            $table->string('order_count');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
