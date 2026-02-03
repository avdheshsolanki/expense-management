<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'balance',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get the user that owns the wallet
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for this wallet
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Add funds to wallet
     */
    public function addFunds($amount, $description = 'Funds added by admin')
    {
        $this->balance += $amount;
        $this->save();

        // Record transaction
        $this->transactions()->create([
            'type' => 'credit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description,
        ]);

        return $this;
    }

    /**
     * Deduct funds from wallet (for expenses)
     */
    public function deductFunds($amount, $description = 'Expense', $expenseId = null)
    {
        $this->balance -= $amount;
        $this->save();

        // Record transaction
        $this->transactions()->create([
            'type' => 'debit',
            'amount' => $amount,
            'balance_after' => $this->balance,
            'description' => $description,
            'expense_id' => $expenseId,
        ]);

        return $this;
    }
}
