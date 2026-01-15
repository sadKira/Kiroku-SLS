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

                    <div class="flex items-center gap-2">

                        <!-- Password -->
                        <flux:input
                            wire:model.defer=""
                            name="password"
                            type="password"
                            required
                            placeholder="Password"
                            viewable
                            readonly
                            disabled
                        />

                        <flux:button size="sm" variant="primary">Reset Password</flux:button>

                    </div>

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
                    
                    <div class="flex items-center gap-2">

                        <!-- Password -->
                        <flux:input
                            wire:model.defer=""
                            name="password"
                            type="password"
                            required
                            placeholder="Password"
                            viewable
                            readonly
                        />

                        <flux:button size="sm" variant="primary">Reset Password</flux:button>

                    </div>

                </div>
            </div> 
        </div>
    </div>

    {{-- Export Dashboard Report Modal --}}
    <flux:modal name="export-dashboard-report" :dismissible="false">
        <flux:heading size="lg" class="mb-4">Enter Private Key</flux:heading>
        
        <div class="space-y-6">
            {{-- Report Type (readonly, set by dropdown) --}}
            <div>
                <flux:field>
                    <flux:input 
                        readonly 
                        type="text" 
                        label="Report Type" 
                        value="{{ $exportReportType === 'monthly' ? 'Monthly Report' : 'Semestral Report' }}"
                        variant="filled"
                        icon:trailing="lock-closed"
                    />
                </flux:field>
            </div>

            {{-- School Year --}}
            <div>
                <flux:field>
                    <flux:select wire:model="exportSchoolYear" label="Select Academic Year" placeholder="Select Academic Year">
                        @foreach ($availableAcademicYears as $academicYear)
                            <flux:select.option class="text-black dark:text-white" value="{{ $academicYear }}">{{ $academicYear }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            </div>

            {{-- Paper Size --}}
            <div>
                <flux:field>
                    <flux:select wire:model="exportPaperSize" label="Select Paper Size" placeholder="Select Paper Size">
                        <flux:select.option class="text-black dark:text-white" value="A4">A4</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="Letter">Letter</flux:select.option>
                        <flux:select.option class="text-black dark:text-white" value="Legal">Legal</flux:select.option>
                    </flux:select>
                </flux:field>
            </div>
        </div>

        <div class="flex gap-2 mt-6">
            <flux:spacer />
            <flux:button variant="ghost" size="sm" wire:click="resetExportForm">Cancel</flux:button>
            <flux:button wire:click="exportDashboardReport" variant="primary" size="sm">
                Export PDF
            </flux:button>
        </div>
    </flux:modal>
</div>
