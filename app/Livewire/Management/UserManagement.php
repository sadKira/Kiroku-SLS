<?php

namespace App\Livewire\Management;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class UserManagement extends Component
{
    public ?int $resetUserId = null;

    public string $newPassword = '';

    public string $newPasswordConfirmation = '';

    /**
     * Open the reset password modal for a specific user
     */
    public function confirmResetPassword(int $userId): void
    {
        $this->resetUserId = $userId;
        $this->newPassword = '';
        $this->newPasswordConfirmation = '';
        $this->resetValidation();

        $this->modal('reset-password')->show();
    }

    /**
     * Reset the password for the selected user
     */
    public function resetPassword(): void
    {
        $this->validate([
            'newPassword' => ['required', 'min:8'],
            'newPasswordConfirmation' => ['required', 'same:newPassword'],
        ], [
            'newPassword.required' => 'Please enter a new password.',
            'newPassword.min' => 'Password must be at least 8 characters.',
            'newPasswordConfirmation.required' => 'Please confirm the password.',
            'newPasswordConfirmation.same' => 'Passwords do not match.',
        ]);

        $user = User::findOrFail($this->resetUserId);

        // Prevent resetting super admin's own password from this interface
        if ($user->role === UserRole::SuperAdmin) {
            $this->dispatch('notify',
                type: 'error',
                content: 'Cannot reset Super Admin password from this interface.',
                duration: 5000
            );

            return;
        }

        $user->update([
            'password' => $this->newPassword,
        ]);

        $this->modal('reset-password')->close();
        $this->reset(['resetUserId', 'newPassword', 'newPasswordConfirmation']);

        $this->dispatch('notify',
            type: 'success',
            content: 'Password has been reset successfully.',
            duration: 5000
        );
    }

    /**
     * Cancel the reset password action
     */
    public function cancelReset(): void
    {
        $this->modal('reset-password')->close();
        $this->reset(['resetUserId', 'newPassword', 'newPasswordConfirmation']);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.management.user-management', [
            'users' => User::where('role', '!=', UserRole::SuperAdmin)->get(),
            'currentUser' => auth()->user(),
        ]);
    }
}
