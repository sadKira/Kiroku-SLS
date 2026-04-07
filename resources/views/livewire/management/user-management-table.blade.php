<div>
    {{-- Table --}}
    <div class="flex flex-col bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 [:where(&)]:p-4 [:where(&)]:rounded-lg">

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th scope="col" class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">User</th>
                                <th scope="col" class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">Role</th>
                                <th scope="col" class="px-3 py-3 text-end text-sm font-medium text-gray-500 dark:text-neutral-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            
                            @if ($users->isEmpty())
                                <tr>
                                    <td colspan="3" class="px-6 py-10 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                        <div class="flex justify-center items-center gap-2 w-full">
                                            <flux:icon.users variant="mini" />
                                            <flux:heading size="lg">No Users</flux:heading>
                                        </div>
                                    </td>
                                </tr>
                            @else
                                @foreach($users as $user)
                                    <tr wire:key="{{ $user->id }}" class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                                        <td class="px-1 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            <div class="flex items-center gap-2 sm:gap-4">
                                                <flux:avatar circle size="lg" class="max-sm:size-8"
                                                    :color="match($user->role) {
                                                        App\Enums\UserRole::Admin => 'blue',
                                                        App\Enums\UserRole::Logger => 'green',
                                                        default => null,
                                                    }"
                                                    :initials="strtoupper(substr($user->name, 0, 2))" />
                                                <div class="flex flex-col">
                                                    <flux:heading>
                                                        {{ $user->name }}
                                                    </flux:heading>
                                                    <flux:text class="max-sm:hidden">{{ $user->username }}</flux:text>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-1 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
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
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-end text-sm font-medium">
                                            <div class="flex items-center justify-end">
                                                <flux:button size="sm" variant="primary" icon="key"
                                                    wire:click="confirmResetPassword({{ $user->id }})"
                                                    wire:target="confirmResetPassword({{ $user->id }})">
                                                    Reset Password
                                                </flux:button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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
