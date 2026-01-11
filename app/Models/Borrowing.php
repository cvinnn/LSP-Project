<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Borrowing Model - Book borrowing transactions
 * 
 * Tracks member borrowing: user_id, book_id, borrow_date, due_date, return_date, status.
 * Note: borrow_date is immutable after creation.
 */
class Borrowing extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        // Prevent borrow_date from being updated
        static::updating(function ($model) {
            if ($model->isDirty('borrow_date')) {
                $model->borrow_date = $model->getOriginal('borrow_date');
            }
        });
    }

    /**
     * User who borrowed the book
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Book that was borrowed
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Check if borrowing is overdue (borrowed + past due_date)
     */
    public function isOverdue()
    {
        return $this->status === 'borrowed' && now()->isAfter($this->due_date);
    }
}

