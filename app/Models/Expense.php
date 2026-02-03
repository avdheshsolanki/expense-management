<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'description',
        'expense_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Get the user that created the expense
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the expense
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the wallet transaction associated with this expense
     */
    public function walletTransaction()
    {
        return $this->hasOne(WalletTransaction::class);
    }

    /**
     * Boot method to handle expense creation
     */
    protected static function booted()
    {
        // Automatically deduct from wallet when expense is created
        static::created(function ($expense) {
            $wallet = $expense->user->wallet;
            if ($wallet) {
                $wallet->deductFunds(
                    $expense->amount,
                    "Expense: {$expense->category->name}",
                    $expense->id
                );
            }
        });
    }
}
