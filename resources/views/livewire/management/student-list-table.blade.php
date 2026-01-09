<div>
    {{-- Table --}}
    <div class="flex flex-col bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 [:where(&)]:p-4 [:where(&)]:rounded-lg">

        <div class="flex items-center justify-between mb-5">

            {{-- Filter --}}
            <div class="flex items-center gap-3">

                <div class="flex items-center gap-2 text-nowrap">
                    <flux:dropdown>
                        <flux:button variant="filled" icon="adjustments-horizontal" size="sm">Filter</flux:button>

                        <flux:menu>

                            <flux:menu.submenu heading="Year Level">
                                <flux:menu.radio.group wire:model.live="selectedYearLevel">
                                    <flux:menu.radio value="1st Year">1st Year</flux:menu.radio>
                                    <flux:menu.radio value="2nd Year">2nd Year</flux:menu.radio>
                                    <flux:menu.radio value="3rd Year">3rd Year</flux:menu.radio>
                                    <flux:menu.radio value="4th Year">4th Year</flux:menu.radio>
                                </flux:menu.radio.group>
                            </flux:menu.submenu>

                            <flux:menu.submenu heading="Course">
                                <flux:menu.radio.group wire:model.live="selectedCourse">
                                    <flux:menu.radio value="Bachelor of Arts in International Studies">
                                        Bachelor of Arts in International Studies</flux:menu.radio>
                                        
                                    <flux:menu.radio value="Bachelor of Science in Information Systems">
                                        Bachelor of Science in Information Systems</flux:menu.radio>

                                    <flux:menu.radio value="Bachelor of Human Services">
                                        Bachelor of Human Services</flux:menu.radio>

                                    <flux:menu.radio value="Bachelor of Secondary Education">
                                        Bachelor of Secondary Education</flux:menu.radio>

                                    <flux:menu.radio value="Bachelor of Elementary Education">
                                        Bachelor of Elementary Education</flux:menu.radio>

                                    <flux:menu.radio value="Bachelor of Special Needs Education">
                                        Bachelor of Special Needs Education</flux:menu.radio>
                                </flux:menu.radio.group>
                            </flux:menu.submenu>

                        </flux:menu>
                    </flux:dropdown>
                </div>
                
                {{-- Filter Indicators --}}
                @if ($selectedYearLevel != 'All')
                    <flux:badge variant="solid" color="zinc">
                        {{ $selectedYearLevel }} <flux:badge.close wire:click="clearYearLevel" />
                    </flux:badge>
                @endif
                @if ($selectedCourse != 'All')
                    <flux:badge variant="solid" color="zinc">
                        {{ $selectedCourse }} <flux:badge.close wire:click="clearCourse" />
                    </flux:badge>
                @endif

            </div>

            {{-- Selection Indicator and Action --}}
            @if ( count($selected) > 0 )

                <div class="flex items-center gap-2">

                    <flux:tooltip content="Clear Selected" position="bottom">
                        <flux:button variant="primary" size="sm" icon="x-mark" wire:click="clearSelected">{{ count($selected) }} selected</flux:button>
                    </flux:tooltip>

                    <flux:button size="sm" icon="trash" variant="danger" wire:click="bulkRemoveProfile">Delete</flux:button>

                </div>
                
            @else

                {{-- Search Students --}}
                <flux:input size="sm" icon="magnifying-glass" placeholder="Search student" class="max-w-100" wire:model.live.debounce.300ms="search" autocomplete="off" clearable />

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
                                    <th scope="col"
                                        class="px-3 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                        <flux:checkbox.all />
                                    </th>
                                    <th scope="col"
                                        class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                        Name</th>
                                    <th scope="col"
                                        class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                        Student ID</th>
                                    <th scope="col"
                                        class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                        Year Level</th>
                                    <th scope="col"
                                        class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                        Course</th>
                                    <th scope="col"
                                            class="px-3 py-3 text-end text-sm font-medium text-gray-500 dark:text-neutral-500">
                                            </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                                
                                @if ($students->isEmpty())

                                    {{-- Search Empty State --}}
                                    @if (!empty($search))

                                        <tr>
                                            <td colspan="6" class="px-6 py-10 whitespace-nowrap text-sm  text-gray-800 dark:text-neutral-200">
                                                <div class="flex justify-center items-center gap-2 w-full">
                                                    <flux:icon.magnifying-glass variant="solid" class="" />
                                                    <flux:heading size="lg">No Student Found</flux:heading>
                                                </div>
                                            </td>
                                        </tr>

                                    {{-- Full Empty Table State --}}
                                    @else

                                        <tr>
                                            <td colspan="6" class="px-6 py-10 whitespace-nowrap text-sm  text-gray-800 dark:text-neutral-200">
                                                <div class="flex justify-center items-center gap-2 w-full">
                                                    <flux:icon.magnifying-glass variant="solid" class="" />
                                                    <flux:heading size="lg">No Students</flux:heading>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                    @endif
                                    
                                @else

                                    @foreach($students as $student)

                                        <tr wire:key="{{ $student->id }}" class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                                            <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                                <flux:checkbox value="{{ $student->id }}" wire:model.live="selected" />
                                            </td>
                                            <td class="px-1 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                                {{ $student->last_name }}, {{ $student->first_name }}
                                            </td>
                                            <td class="px-1 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                {{ $student->id_student }}
                                            </td>
                                            <td class="px-1 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                {{ $student->year_level }}
                                            </td>
                                            <td class="px-1 py-3 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                                {{ $student->course }}
                                            </td>
                                            @if( count($selected) < 1 )
                                                <td class="px-3 py-3 whitespace-nowrap text-end text-sm font-medium">
                                                    <div class="flex items-center justify-end">
                                                        
                                                        <flux:link wire:click="editProfile({{ $student->id }})" class="cursor-pointer">Edit</flux:link>
                                                        <div 
                                                            x-data="{ isHovered: false }" 
                                                            @mouseenter="isHovered = true" 
                                                            @mouseleave="isHovered = false"
                                                        >
                                                            <!-- Outline variant shown by default -->
                                                            <flux:tooltip content="Delete Student" position="top">
                                                                <flux:icon.trash 
                                                                    x-show="!isHovered" 
                                                                    variant="outline" 
                                                                    class="text-red-500 cursor-pointer ml-5" 
                                                                />
                                                            </flux:tooltip>

                                                            <!-- Solid variant shown only on hover -->
                                                            <flux:tooltip content="Delete Student" position="top">
                                                                <flux:icon.trash 
                                                                    x-show="isHovered" 
                                                                    variant="solid" 
                                                                    class="text-red-500 cursor-pointer ml-5" 
                                                                    x-cloak 
                                                                    wire:click="removeProfile({{  $student->id }})"
                                                                />
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

                    @if ($students->hasPages())
                        
                        <div class="mt-4">
                            {{ $students->links('pagination::tailwind') }}
                        </div>

                     @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Edit Student Details Modal --}}
    <flux:modal name="update-student" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Edit Student Details</flux:heading>
            </div>

            {{-- Name --}}
            <flux:input wire:model.defer="last_name" type="text" label="Last Name" placeholder="Last Name" />

            <flux:input wire:model.defer="first_name" type="text" label="First Name" placeholder="First Name" />

            {{-- Student ID --}}
            <flux:input wire:model.defer="id_student" type="text" label="Student ID" mask="9999999" placeholder="7-Digit ID" />

            {{-- Year Level --}}
            <flux:select wire:model.defer="year_level" label="Year level" placeholder="Year Level">
                <flux:select.option class="text-black dark:text-white" value="1st Year">1st Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="2nd Year">2nd Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="3rd Year">3rd Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="4th Year">4th Year</flux:select.option>
            </flux:select>

            {{-- Course --}}
            <flux:select wire:model.defer="course" label="Course" placeholder="Course">
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Arts in International Studies">Bachelor of Arts in International Studies</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Science in Information Systems">Bachelor of Science in Information Systems</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Human Services">Bachelor of Human Services</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Secondary Education">Bachelor of Secondary Education</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Elementary Education">Bachelor of Elementary Education</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="Bachelor of Special Needs Education">Bachelor of Special Needs Education</flux:select.option>
            </flux:select>

            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="updateProfileInformation" variant="primary" size="sm">
                    Update
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Remove Student Modal --}}
    <flux:modal name="remove-student" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Student?</flux:heading>
                <flux:text class="mt-2">
                    You are about to delete <span class="font-bold">{{ $last_name ?? 'error' }}, {{ $first_name ?? 'error' }}</span>.
                </flux:text>
            </div>
            
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteProfileInformation" variant="danger" size="sm">
                    Delete
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Bulk Remove Student Modal --}}
    <flux:modal name="bulkremove-student" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Selected?</flux:heading>
                <flux:text class="mt-2">
                    You are about to delete <span class="font-bold">{{ count($selected) }} students</span>.
                </flux:text>
            </div>
            
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost" size="sm">Cancel</flux:button>
                </flux:modal.close>
                <flux:button wire:click="bulkDeleteProfileInformation" variant="danger" size="sm">
                    Delete
                </flux:button>
            </div>
        </div>
    </flux:modal>

</div>
