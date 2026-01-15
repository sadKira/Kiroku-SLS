<div>
    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('admin_dashboard') }}" wire:navigate>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Super Administrator</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item>Manage Accounts</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        <x-management.profile-section />

    </div>

    <div class="max-w-5xl mx-auto px-4">
        <div class=" bg-white border border-black/10 [:where(&)]:p-4 [:where(&)]:rounded-lg p-5">

            <flux:heading class="mb-3">Authenticated Account</flux:heading>
        
            {{-- Super Admin Account --}}
            <div class="flex mb-5 gap-2 text-sm font-medium text-gray-800 dark:text-neutral-200">

                <flux:profile circle class="" initials="SA" :chevron="false" />

                <div class="flex flex-col">

                    <div class="flex items-center gap-1 ">

                        <div class="font-bold">Super Administrator</div>

                        <flux:icon.shield-check variant="micro" />

                    </div>

                    <div class="text-zinc-400">Super Administrator Account</div>

                </div>
            </div>

            <flux:heading class="mb-3">Accounts</flux:heading>

            <div class="flex flex-col space-y-4">
                <div class="flex items-center justify-between">

                    {{-- Admin Account --}}
                    <div class="flex flex-col gap-1">
                        
                        <div class="flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-neutral-200">

                            <flux:profile circle class="" initials="AA" :chevron="false" />

                            <div class="flex flex-col">

                                <div class="font-bold">mkd2025-admin</div>
                                <div class="text-zinc-400">Administrator Account</div>

                            </div>
                        </div>
                    </div>

                    <flux:button size="sm" variant="primary">Reset Password</flux:button>

                </div>

                <div class="flex items-center justify-between">

                    {{-- Logger Account --}}
                    <div class="flex flex-col">
                        <div class="flex gap-2 text-sm font-medium text-gray-800 dark:text-neutral-200">

                            <flux:profile circle class="" initials="LA" :chevron="false" />

                            <div class="flex flex-col">
                                <div class="font-bold">mkd2025-logger</div>
                                <div class="text-zinc-400">Student Logger Account</div>
                            </div>
                        </div>

                    </div>

                    <flux:button size="sm" variant="primary">Reset Password</flux:button>

                </div>
            </div> 
        </div>
    </div>

</div>
