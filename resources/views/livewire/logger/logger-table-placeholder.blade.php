<div>
    {{-- Two Column Layout Placeholder --}}
    <div class="flex flex-col lg:flex-row gap-6 mt-5">

        {{-- Left Column: Filters Placeholder --}}
        <div class="w-full lg:w-80 flex-shrink-0">
            <div class="bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 rounded-lg p-4 sticky top-4">
                
                {{-- Search Bar Placeholder --}}
                <div class="mb-6">
                    <flux:skeleton.group animate="shimmer">
                        <flux:skeleton.line class="w-16 h-5 mb-2" />
                        <flux:skeleton.line class="w-full h-8" />
                    </flux:skeleton.group>
                </div>

                {{-- Filters Section Placeholder --}}
                <div class="space-y-4">
                    <flux:skeleton.group animate="shimmer">
                        <flux:skeleton.line class="w-20 h-5 mb-3" />
                    </flux:skeleton.group>

                    {{-- Select Month Placeholder --}}
                    <div class="flex flex-col gap-2">
                        <flux:skeleton.group animate="shimmer">
                            <flux:skeleton.line class="w-16 h-4" />
                            <flux:skeleton.line class="w-full h-8 mt-2" />
                        </flux:skeleton.group>
                    </div>

                    {{-- Select Year Placeholder --}}
                    <div class="flex flex-col gap-2">
                        <flux:skeleton.group animate="shimmer">
                            <flux:skeleton.line class="w-12 h-4" />
                            <flux:skeleton.line class="w-full h-8 mt-2" />
                        </flux:skeleton.group>
                    </div>

                    {{-- Select Academic Year Placeholder --}}
                    <div class="flex flex-col gap-2">
                        <flux:skeleton.group animate="shimmer">
                            <flux:skeleton.line class="w-24 h-4" />
                            <flux:skeleton.line class="w-full h-8 mt-2" />
                        </flux:skeleton.group>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Log Session Cards Placeholder --}}
        <div class="flex-1">
            {{-- Cards Grid Placeholder --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @for($i = 0; $i < 6; $i++)
                    <div class="bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 rounded-lg p-5">
                        {{-- Date Placeholder --}}
                        <div class="mb-3">
                            <flux:skeleton.group animate="shimmer">
                                <flux:skeleton.line class="w-3/4 h-6 mb-2" />
                                <flux:skeleton.line class="w-1/2 h-5" />
                            </flux:skeleton.group>
                        </div>

                        {{-- School Year Placeholder --}}
                        <div class="mb-4">
                            <flux:skeleton.group animate="shimmer">
                                <flux:skeleton.line class="w-2/3 h-4" />
                            </flux:skeleton.group>
                        </div>

                        {{-- Student Count Placeholder --}}
                        <div class="flex items-center justify-end gap-2 pt-4 border-t border-black/10 dark:border-white/10">
                            <flux:skeleton.group animate="shimmer">
                                <flux:skeleton.line class="w-24 h-4" />
                            </flux:skeleton.group>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>
