<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade'); // Wallet associated with transaction
            $table->enum('type', ['credit', 'debit']); // Transaction type (credit: add funds, debit: expense)
            $table->decimal('amount', 10, 2); // Transaction amount
            $table->decimal('balance_after', 10, 2); // Balance after this transaction
            $table->string('description')->nullable(); // Transaction description
            $table->foreignId('expense_id')->nullable()->constrained()->onDelete('set null'); // Link to expense if applicable
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
        Schema::dropIfExists('wallet_transactions');
    }
}
