<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Super Administrator</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>User Management</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    {{-- Upper --}}
    <div class="flex justify-between items-center mb-5">
        <flux:heading size="xl">User Management</flux:heading>
    </div>

    {{-- User Table --}}
    <flux:table>
        <flux:table.columns>
            <flux:table.column>User</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column class="text-right">Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row>
                    <flux:table.cell>
                        <div class="flex items-center gap-2 sm:gap-4">
                            <flux:avatar circle size="lg" class="max-sm:size-8"
                                :initials="strtoupper(substr($user->name, 0, 2))" />
                            <div class="flex flex-col">
                                <flux:heading>
                                    {{ $user->name }}
                                </flux:heading>
                                <flux:text class="max-sm:hidden">{{ $user->username }}</flux:text>
                            </div>
                        </div>
                    </flux:table.cell>

                    <flux:table.cell>
                        @switch($user->role)
                            @case(App\Enums\UserRole::Admin)
                                <flux:badge size="sm" color="blue">Admin</flux:badge>
                                @break
                            @case(App\Enums\UserRole::Logger)
                                <flux:badge size="sm" color="green">Logger</flux:badge>
                                @break
                            @default
                                <flux:badge size="sm">{{ $user->role->value }}</flux:badge>
                        @endswitch
                    </flux:table.cell>

                    <flux:table.cell>
                        <div class="flexitems-center gap-2">
                            <flux:button size="sm" variant="primary" icon="key"
                                wire:click="confirmResetPassword({{ $user->id }})"
                                wire:target="confirmResetPassword({{ $user->id }})">
                                Reset Password
                            </flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3" class="text-center">
                        <flux:text>No users found.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    {{-- Reset Password Modal --}}
    <flux:modal name="reset-password" :dismissible="false" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Reset Password</flux:heading>
                <flux:text class="mt-1">Enter a new password for this account.</flux:text>
            </div>

            <flux:input wire:model.defer="newPassword" type="password" label="New Password"
                placeholder="Enter new password" />

            <flux:input wire:model.defer="newPasswordConfirmation" type="password" label="Confirm Password"
                placeholder="Confirm new password" />

            <div class="flex gap-2">
                <flux:spacer />
                <flux:button variant="ghost" size="sm" wire:click="cancelReset">Cancel</flux:button>
                <flux:button wire:click="resetPassword" variant="primary" size="sm">
                    Reset Password
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
