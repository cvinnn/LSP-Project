<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * UserController - Manage members (list, block/allow, view profile)
 */
class UserController extends BaseController
{
    /**
     * Show all members with borrowing statistics
     */
    public function index()
    {
        try {
            $users = User::withCount('borrowings')->get();
            return view('users.index', compact('users'));
        } catch (\Exception $e) {
            \Log::error('Error fetching users: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading users');
        }
    }

    /**
     * Toggle member borrowing permission (with protection against active borrowings)
     */
    public function togglePermission(User $user)
    {
        try {
            // Check if user has active borrowings and trying to block
            if ($user->can_borrow) {
                $activeBorrowings = $user->borrowings()->where('status', 'borrowed')->count();
                if ($activeBorrowings > 0) {
                    return redirect()->back()->with('error', $user->name . ' has ' . $activeBorrowings . ' active borrowing(s). Cannot block user until all books are returned.');
                }
            }
            
            $user->update(['can_borrow' => !$user->can_borrow]);
            
            $status = $user->can_borrow ? 'enabled' : 'disabled';
            \Log::info('Borrowing permission ' . $status . ' for user: ' . $user->name);
            
            return redirect()->back()->with('success', 'Borrowing permission ' . $status . ' for ' . $user->name);
        } catch (\Exception $e) {
            \Log::error('Error toggling user permission: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating user permission');
        }
    }

    /**
     * Show member profile with borrowing history
     */
    public function show(User $user)
    {
        try {
            $borrowings = $user->borrowings()->with('book')->get();
            return view('users.show', compact('user', 'borrowings'));
        } catch (\Exception $e) {
            \Log::error('Error fetching user details: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading user details');
        }
    }

    /**
     * Show member profile dengan borrowing history terpisah
     * 
     * @param \App\Models\User $user
     * @return \Illuminate\View\View members.profile view
     */
    public function memberProfile(User $user)
    {
        try {
            $activeBorrowings = $user->borrowings()->where('status', 'borrowed')->with('book')->get();
            $returnedBorrowings = $user->borrowings()->where('status', 'returned')->with('book')->get();
            
            return view('members.profile', compact('user', 'activeBorrowings', 'returnedBorrowings'));
        } catch (\Exception $e) {
            \Log::error('Error fetching member profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading profile');
        }
    }
}
