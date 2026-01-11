<?php

namespace App\Traits;

/**
 * LoggableTrait - Provides logging functionality
 * 
 * Can be used in models to automatically log changes
 */
trait LoggableTrait
{
    /**
     * Log an action with timestamp
     */
    public function logAction(string $action, ?string $details = null): void
    {
        $message = $action;
        if ($details) {
            $message .= ': ' . $details;
        }
        \Log::info($message);
    }
}

/**
 * TimestampableTrait - Manage timestamps
 */
trait TimestampableTrait
{
    /**
     * Get formatted created_at date
     */
    public function getCreatedDate(): string
    {
        return $this->created_at->format('M d, Y');
    }

    /**
     * Get formatted updated_at date
     */
    public function getUpdatedDate(): string
    {
        return $this->updated_at->format('M d, Y');
    }
}

/**
 * StatusableTrait - Manage statuses
 */
trait StatusableTrait
{
    /**
     * Check if status is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->status === 'borrowed';
    }

    /**
     * Check if status is inactive/returned
     */
    public function isInactive(): bool
    {
        return $this->status === 'returned' || $this->status === 'inactive';
    }
}
