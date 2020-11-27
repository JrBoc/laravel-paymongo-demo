<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->decimal('amount',10, 2);
            $table->string('type');
            $table->string('transaction_id')->unique();
            $table->json('payload');
            $table->string('src_id')->unique()->nullable();
            $table->string('pi_id')->unique()->nullable();
            $table->string('pay_id')->unique()->nullable();
            $table->json('initial_response');
            $table->json('source_response');
            $table->json('intent_payment_response');
            $table->json('payment_response');
            $table->json('re_query_response');
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('app_payments');
    }
}
