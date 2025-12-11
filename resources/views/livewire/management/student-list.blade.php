<div>

    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item href="{{ route('home') }}">Home</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item href="{{ route('student_list') }}">Students List</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

        {{-- Profile Section --}}
        <x-management.profile-section />

    </div>

    {{-- Upper Cards --}}
    <div class="flex items-center justify-between mb-10">

        {{-- <x-ui.card size="xl" class="">

            <div class="flex items-center gap-20">
                <div class="flex items-center gap-2">
                    <flux:icon.user-group />
                    <flux:heading size="lg">Total Students</flux:heading>
                </div>
                <flux:heading size="xl">60%</flux:heading>
            </div>

        </x-ui.card> --}}

        <flux:heading size="xl">Student List</flux:heading>

        <flux:modal.trigger name="create-students">
            <flux:button icon="plus">Add Students</flux:button>
        </flux:modal.trigger>

    </div>

    {{-- Create Students Modal --}}
    <flux:modal name="create-students" :dismissible="false"
        class="min-w-[22rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Add Student</flux:heading>
            </div>

            {{-- Name --}}
            <flux:input type="text" label="Student Name" placeholder="Student Name" />

            {{-- Student ID --}}
            <flux:input type="text" label="Student ID" mask="9999999" placeholder="7-Digit ID" />

            {{-- Year Level --}}
            <flux:select label="Year level" placeholder="Year Level">
                <flux:select.option class="text-black dark:text-white" value="1st Year">1st Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="2nd Year">2nd Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="3rd Year">3rd Year</flux:select.option>
                <flux:select.option class="text-black dark:text-white" value="4th Year">4th Year</flux:select.option>
            </flux:select>

            {{-- Course --}}
            <flux:select label="Course" placeholder="Course">
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
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button variant="primary">
                    Add
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Table --}}
    <div class="flex flex-col bg-white dark:bg-zinc-800 border border-black/10 dark:border-white/10 [:where(&)]:p-4 [:where(&)]:rounded-lg">

        <div class="flex items-center justify-between mb-5">

            {{-- Filter --}}
            <flux:dropdown>
                <flux:button icon="adjustments-horizontal">Filter</flux:button>

                <flux:menu>

                    <flux:menu.submenu heading="Year Level">
                        <flux:menu.radio.group>
                            <flux:menu.radio checked>Name</flux:menu.radio>
                            <flux:menu.radio>Date</flux:menu.radio>
                            <flux:menu.radio>Popularity</flux:menu.radio>
                        </flux:menu.radio.group>
                    </flux:menu.submenu>

                    <flux:menu.submenu heading="Course">
                        <flux:menu.checkbox checked>Draft</flux:menu.checkbox>
                        <flux:menu.checkbox checked>Published</flux:menu.checkbox>
                        <flux:menu.checkbox>Archived</flux:menu.checkbox>
                    </flux:menu.submenu>

                </flux:menu>
            </flux:dropdown>
   
            {{-- Search Students --}}
            <flux:input icon="magnifying-glass" placeholder="Search students" class="max-w-100" />

        </div>

        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Name</th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Student ID</th>
                                <th scope="col"
                                    class="px-6 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Year Level</th>
                                    <th scope="col"
                                    class="px-6 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Course</th>
                                <th scope="col"
                                    class="px-6 py-3 text-end text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            
                            <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    John Brown
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    1234567
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    2nd Year
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    Bachelor of Science in Information Systems
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- <flux:button variant="ghost" size="sm">Edit</flux:button> --}}
                                        <x-ui.button variant="outline" size="sm">
                                            Edit
                                        </x-ui.button>
                                        <flux:button icon="trash" variant="danger" size="sm"></flux:button>
                                        
                                    </div>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    John Brown
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    1234567
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    2nd Year
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    Bachelor of Science in Information Systems
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                    <flux:button icon="ellipsis-vertical" variant="ghost" size="sm"></flux:button>
                                </td>
                            </tr>

                            <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    John Brown
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    1234567
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    2nd Year
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    Bachelor of Science in Information Systems
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                    <flux:button icon="ellipsis-vertical" variant="ghost" size="sm"></flux:button>
                                </td>
                            </tr>
                           
                            <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    John Brown
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    1234567
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    2nd Year
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                    Bachelor of Science in Information Systems
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                    <flux:button icon="ellipsis-vertical" variant="ghost" size="sm"></flux:button>
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
