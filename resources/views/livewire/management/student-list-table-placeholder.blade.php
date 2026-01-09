<div>
    {{-- Table Placeholder --}}
    <div class="flex flex-col bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 [:where(&)]:p-4 [:where(&)]:rounded-lg">

        <div class="flex items-center justify-between mb-5">
            {{-- Filter Placeholder --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:skeleton.group animate="shimmer">
                        <flux:skeleton.line class="w-20 h-8" />
                    </flux:skeleton.group>
                </div>
            </div>

            {{-- Search Placeholder --}}
            <flux:skeleton.group animate="shimmer">
                <flux:skeleton.line class="w-48 h-8" />
            </flux:skeleton.group>
        </div>

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-3 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    <flux:skeleton.group animate="shimmer">
                                        <flux:skeleton.line class="w-4 h-4" />
                                    </flux:skeleton.group>
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
                            @for($i = 0; $i < 5; $i++)
                                <tr>
                                    <td class="px-3 py-3">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line class="w-4 h-4" />
                                        </flux:skeleton.group>
                                    </td>
                                    <td class="px-1 py-3">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line />
                                        </flux:skeleton.group>
                                    </td>
                                    <td class="px-1 py-3">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line />
                                        </flux:skeleton.group>
                                    </td>
                                    <td class="px-1 py-3">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line />
                                        </flux:skeleton.group>
                                    </td>
                                    <td class="px-1 py-3">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line class="w-3/4" />
                                        </flux:skeleton.group>
                                    </td>
                                    <td class="px-3 py-3">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line class="w-16 ml-auto" />
                                        </flux:skeleton.group>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

