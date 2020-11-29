<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEWalletPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_ewallet_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('type');
            $table->decimal('amount',10, 2);
            $table->string('transaction_id')->unique();
            $table->json('payload')->nullable();
            $table->string('src_id')->unique()->nullable();
            $table->string('pay_id')->unique()->nullable();
            $table->json('source_response')->nullable();
            $table->json('source_callback_response')->nullable();
            $table->json('payment_response')->nullable();
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
        Schema::dropIfExists('app_ewallet_payments');
    }
}
