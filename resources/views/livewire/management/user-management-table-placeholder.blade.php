<div>
    {{-- Table Placeholder --}}
    <div class="flex flex-col bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 [:where(&)]:p-4 [:where(&)]:rounded-lg">

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class=" overflow-hidden dark:border-neutral-700">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th scope="col" class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    <flux:skeleton.group animate="shimmer">
                                        <flux:skeleton.line class="w-32 h-4" />
                                    </flux:skeleton.group>
                                </th>
                                <th scope="col" class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    <flux:skeleton.group animate="shimmer">
                                        <flux:skeleton.line class="w-20 h-4" />
                                    </flux:skeleton.group>
                                </th>
                                <th scope="col" class="px-3 py-3 text-end text-sm font-medium text-gray-500 dark:text-neutral-500"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            @for($i = 0; $i < 2; $i++)
                                <tr>
                                    <td class="px-1 py-3">
                                        <div class="flex items-center gap-4">
                                            <flux:skeleton.group animate="shimmer">
                                                <flux:skeleton.line class="size-10 rounded-full" />
                                            </flux:skeleton.group>
                                            <flux:skeleton.group animate="shimmer" class="w-32">
                                                <flux:skeleton.line class="h-4 mb-2" />
                                                <flux:skeleton.line class="h-3 w-2/3" />
                                            </flux:skeleton.group>
                                        </div>
                                    </td>
                                    <td class="px-1 py-3">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line class="w-16 h-6 rounded-md" />
                                        </flux:skeleton.group>
                                    </td>
                                    <td class="px-3 py-3 text-end">
                                        <flux:skeleton.group animate="shimmer">
                                            <flux:skeleton.line class="w-24 h-8 ml-auto rounded-md" />
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
