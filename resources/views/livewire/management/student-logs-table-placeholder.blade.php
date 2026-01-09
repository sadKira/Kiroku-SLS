<div>    
    {{-- Table Placeholder --}}
    <div class="flex flex-col">

        <div class="flex items-center justify-between mb-5">

            {{-- Filter --}}
            <div class="flex items-center gap-3">

                {{-- Select Month --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Month:</flux:heading>
                    <flux:skeleton.group animate="shimmer">
                        <flux:skeleton.line class="w-40 h-8" />
                    </flux:skeleton.group>
                </div>

                {{-- Select Year --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Year:</flux:heading>
                    <flux:skeleton.group animate="shimmer">
                        <flux:skeleton.line class="w-40 h-8" />
                    </flux:skeleton.group>
                </div>

                {{-- Select Academic Year --}}
                <div class="flex items-center gap-2 text-nowrap">
                    <flux:heading>Academic Year:</flux:heading>
                    <flux:skeleton.group animate="shimmer">
                        <flux:skeleton.line class="w-40 h-8" />
                    </flux:skeleton.group>
                </div>

            </div>

            {{-- Add Logs --}}
            <flux:skeleton.group animate="shimmer">
                <flux:skeleton.line class="w-30 h-8" />
            </flux:skeleton.group>

        </div>

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                    <table class="min-w-full border-separate border-spacing-y-[10px] -mt-2.5">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-3 py-1 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Log</th>
                                <th scope="col"
                                    class="px-3 py-1 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Academic Year</th>
                                <th scope="col"
                                    class="px-3 py-1 text-end text-sm font-medium text-gray-500 dark:text-neutral-500">
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            @for($i = 0; $i < 5; $i++)
                                <tr class="bg-white dark:bg-zinc-900">

                                    <td class="px-3 py-4 border-t border-b border-black/10 dark:border-white/10 border-l rounded-l-lg">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line />
                                        </flux:skeleton.group>
                                    </td>

                                    <td class="px-3 py-4 border-t border-b border-black/10 dark:border-white/10">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line />
                                        </flux:skeleton.group>
                                    </td>

                                    <td
                                        class="px-3 py-4 border-t border-b border-black/10 dark:border-white/10 border-r rounded-r-lg">
                                        <div class="flex items-center justify-end">
                                            <div class="flex items-center gap-1">
                                                <flux:skeleton.group animate="shimmer">
                                                    <flux:skeleton.line class="w-16 ml-auto" />
                                                </flux:skeleton.group>
                                            </div>

                                        </div>
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