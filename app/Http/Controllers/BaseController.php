<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as IlluminateController;

/**
 * BaseController - Base class for all controllers with utility methods
 */
class BaseController extends IlluminateController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Handle errors with logging and redirect
     */
    protected function handleError(\Exception $e, string $redirect, string $message = 'An error occurred'): \Illuminate\Http\RedirectResponse
    {
        \Log::error($message . ': ' . $e->getMessage());
        return redirect()->route($redirect)->with('error', $message);
    }

    /**
     * Log an action
     */
    protected function logAction(string $action): void
    {
        \Log::info($action);
    }
}
