<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Book Model - Book inventory with quantity and available count tracking
 */
class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'quantity',
        'available',
    ];

    /**
     * Get all borrowing transactions for this book
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Check if book has available copies
     */
    public function isAvailable()
    {
        return $this->available > 0;
    }
}
