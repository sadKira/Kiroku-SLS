<div>
    {{-- Table --}}
    <div class="flex flex-col bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 [:where(&)]:p-4 [:where(&)]:rounded-lg">

        <div class="flex items-center justify-between mb-5">

            @if ($hasFaculties && empty($search))

                {{-- Filter --}}
                <div class="flex items-center gap-3">

                    <div class="flex items-center gap-2 text-nowrap">
                        <flux:dropdown>
                            <flux:button variant="filled" icon="adjustments-horizontal" size="sm">Filter</flux:button>

                            <flux:menu>
                                <flux:menu.submenu heading="Instructional Level">
                                    <flux:menu.radio.group wire:model.live="selectedInstructionalLevel">
                                        @foreach($instructionalLevels as $level)
                                            <flux:menu.radio value="{{ $level->name }}">
                                                {{ $level->name }}</flux:menu.radio>
                                        @endforeach
                                    </flux:menu.radio.group>
                                </flux:menu.submenu>
                            </flux:menu>
                        </flux:dropdown>
                    </div>
                    
                    {{-- Filter Indicators --}}
                    @if ($selectedInstructionalLevel != 'All')
                        <flux:badge variant="solid" color="zinc">
                            {{ $selectedInstructionalLevel }} <flux:badge.close wire:click="clearInstructionalLevel" />
                        </flux:badge>
                    @endif

                </div>

            @endif

            {{-- Selection Indicator and Action --}}
            @if ( count($selected) > 0 )

                <div class="flex items-center gap-2">
                    <flux:tooltip content="Clear Selected" position="bottom">
                        <flux:button variant="primary" size="sm" icon="x-mark" wire:click="clearSelected">{{ count($selected) }} selected</flux:button>
                    </flux:tooltip>
                    <flux:button size="sm" icon="trash" variant="danger" wire:click="bulkRemoveProfile">Delete</flux:button>
                </div>
                
            @else

                @if ($faculties->isNotEmpty() || !empty($search))
                    <flux:input size="sm" icon="magnifying-glass" placeholder="name, faculty id, instructional level, etc." class="max-w-100" wire:model.live.debounce.300ms="search" autocomplete="off" clearable />
                @endif

            @endif
            
        </div>

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                    <flux:checkbox.group>
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th scope="col" class="px-3 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                        <flux:checkbox.all />
                                    </th>
                                    <th scope="col" class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">Name</th>
                                    <th scope="col" class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">Faculty ID</th>
                                    <th scope="col" class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">Instructional Level</th>
                                    <th scope="col" class="px-3 py-3 text-end text-sm font-medium text-gray-500 dark:text-neutral-500"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                
                                @if ($faculties->isEmpty())
                                    @if (!empty($search))
                                        <tr>
                                            <td colspan="5" class="px-6 py-10 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                <div class="flex justify-center items-center gap-2 w-full">
                                                    <flux:icon.user-search variant="mini" />
                                                    <flux:heading size="lg">No Faculty Found</flux:heading>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="5" class="px-6 py-10 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                <div class="flex justify-center items-center gap-2 w-full">
                                                    <flux:icon.users variant="mini" />
                                                    <flux:heading size="lg">No Faculty</flux:heading>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    @foreach($faculties as $faculty)
                                        <tr wire:key="{{ $faculty->id }}" class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                                <flux:checkbox value="{{ $faculty->id }}" wire:model.live="selected" />
                                            </td>
                                            <td class="px-1 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                                {{ $faculty->last_name }}, {{ $faculty->first_name }}
                                            </td>
                                            <td class="px-1 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                {{ $faculty->id_faculty }}
                                            </td>
                                            <td class="px-1 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                {{ $faculty->instructional_level }}
                                            </td>
                                            @if( count($selected) < 1 )
                                                <td class="px-3 py-3 whitespace-nowrap text-end text-sm font-medium">
                                                    <div class="flex items-center justify-end">
                                                        <flux:link wire:click="editProfile({{ $faculty->id }})" class="cursor-pointer">Edit</flux:link>
                                                        <div x-data="{ isHovered: false }" @mouseenter="isHovered = true" @mouseleave="isHovered = false">
                                                            <flux:tooltip content="Delete Faculty" position="top">
                                                                <flux:icon.trash x-show="!isHovered" variant="outline" class="text-red-500 cursor-pointer ml-5" />
                                                            </flux:tooltip>
                                                            <flux:tooltip content="Delete Faculty" position="top">
                                                                <flux:icon.trash x-show="isHovered" variant="solid" class="text-red-500 cursor-pointer ml-5" x-cloak wire:click="removeProfile({{ $faculty->id }})" />
                                                            </flux:tooltip>
                                                        </div>
                                                    </div>
                                                </td>
                                            @else
                                                <td class="px-3 py-3 whitespace-nowrap text-end text-sm font-medium">
                                                    <div class="flex items-center justify-end">
                                                        <flux:link class="opacity-0 pointer-events-none">Edit</flux:link>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </flux:checkbox.group>

                    @if ($faculties->hasPages())
                        <div class="mt-4">
                            {{ $faculties->links('pagination::tailwind') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Edit Faculty Details Modal --}}
    <flux:modal name="update-faculty" :dismissible="false" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Faculty Details</flux:heading>
            </div>

            <flux:input wire:model.defer="last_name" type="text" label="Last Name" placeholder="Last Name" />
            <flux:input wire:model.defer="first_name" type="text" label="First Name" placeholder="First Name" />
            <flux:input wire:model.defer="id_faculty" type="text" label="Faculty ID" readonly variant="filled" icon:trailing="lock-closed" />

            <flux:select wire:model.defer="instructional_level" label="Instructional Level" placeholder="Instructional Level">
                @foreach($instructionalLevels as $level)
                    <flux:select.option class="text-black dark:text-white" value="{{ $level->name }}">{{ $level->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="updateProfileInformation" variant="primary" size="sm">Update</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Remove Faculty Modal --}}
    <flux:modal name="remove-faculty" :dismissible="false" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Faculty?</flux:heading>
                <flux:text class="mt-2">
                    You are about to delete <span class="font-bold">{{ $last_name ?? 'error' }}, {{ $first_name ?? 'error' }}</span>.
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteProfileInformation" variant="danger" size="sm">Delete</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Bulk Remove Faculty Modal --}}
    <flux:modal name="bulkremove-faculty" :dismissible="false" class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Selected?</flux:heading>
                <flux:text class="mt-2">
                    You are about to delete <span class="font-bold">{{ count($selected) }} faculty member(s)</span>.
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="bulkDeleteProfileInformation" variant="danger" size="sm">Delete</flux:button>
            </div>
        </div>
    </flux:modal>

</div>
