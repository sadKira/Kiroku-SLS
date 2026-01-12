<div>
    {{-- Log Records Placeholder --}}
    <div class="space-y-3">
        @for($i = 0; $i < 8; $i++)
            <div class="bg-white dark:bg-zinc-800 border border-black/10 dark:border-white/10 rounded-lg p-4 mr-5">
                <div class="flex items-center justify-between gap-4">
                    {{-- Student Info Placeholder --}}
                    <div class="flex justify-center">
                        <div class="flex items-center gap-3">
                            <div>
                                <flux:skeleton.group animate="shimmer">
                                    <flux:skeleton.line class="w-48 h-5 mb-2" />
                                    <flux:skeleton.line class="w-32 h-4" />
                                </flux:skeleton.group>
                            </div>
                        </div>
                    </div>

                    {{-- Time In/Out Placeholder --}}
                    <div class="flex items-center gap-4">
                        {{-- Time In Placeholder --}}
                        <div class="flex items-center gap-2">
                            <flux:skeleton.group animate="shimmer">
                                <flux:skeleton.line class="w-16 h-4" />
                            </flux:skeleton.group>
                        </div>

                        {{-- Time Out Placeholder --}}
                        <div class="flex items-center gap-2">
                            <flux:skeleton.group animate="shimmer">
                                <flux:skeleton.line class="w-16 h-4" />
                            </flux:skeleton.group>
                        </div>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>
