<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_card_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->decimal('amount',10, 2);
            $table->string('transaction_id')->unique();
            $table->json('payload')->nullable();
            $table->string('pm_id')->unique()->nullable();
            $table->string('pi_id')->unique()->nullable();
            $table->boolean('payment_attached')->default(false);
            $table->json('payment_method_response')->nullable();
            $table->json('payment_intent_response')->nullable();
            $table->json('re_query_response')->nullable();
            $table->string('status')->default('initialized');
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
        Schema::dropIfExists('app_card_payments');
    }
}
