<div>
    <div class="flex flex-col bg-white dark:bg-zinc-900 border border-black/10 dark:border-white/10 [:where(&)]:p-4 [:where(&)]:rounded-lg">

        <div class="flex items-center justify-between mb-5">
            {{-- Search Input --}}
            @if ($strands->isNotEmpty() || !empty($search))
                <flux:input size="sm" icon="magnifying-glass" placeholder="Search strands..." class="max-w-100" wire:model.live.debounce.300ms="search" autocomplete="off" clearable />
            @endif
            
            {{-- Search Placeholder to push UI items right if needed, or put something here later --}}
            <div></div> 
        </div>

        {{-- Table Contents --}}
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
                <div class="overflow-hidden dark:border-neutral-700">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th scope="col"
                                    class="px-3 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Code</th>
                                <th scope="col"
                                    class="px-1 py-3 text-start text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    Name</th>
                                <th scope="col"
                                    class="px-3 py-3 text-end text-sm font-medium text-gray-500 dark:text-neutral-500">
                                    </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                            
                            @if ($strands->isEmpty())

                                {{-- Search Empty State --}}
                                @if (!empty($search))
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            <div class="flex justify-center items-center gap-2 w-full">
                                                <flux:icon.magnifying-glass variant="mini" class="" />
                                                <flux:heading size="lg">No Strands Found</flux:heading>
                                            </div>
                                        </td>
                                    </tr>

                                {{-- Full Empty Table State --}}
                                @else
                                    <tr>
                                        <td colspan="3" class="px-6 py-10 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                            <div class="flex justify-center items-center gap-2 w-full">
                                                <flux:icon.clipboard-document-list variant="mini" class="" />
                                                <flux:heading size="lg">No Strands Available</flux:heading>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                
                            @else

                                @foreach($strands as $strand)
                                    <tr wire:key="{{ $strand->id }}" class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                                        <td class="px-3 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            <flux:badge size="sm" color="zinc">{{ $strand->code }}</flux:badge>
                                        </td>
                                        <td class="px-1 py-3 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">
                                            {{ $strand->name }}
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-end text-sm font-medium">
                                            <div class="flex items-center justify-end">
                                                <div 
                                                    x-data="{ isHovered: false }" 
                                                    @mouseenter="isHovered = true" 
                                                    @mouseleave="isHovered = false"
                                                >
                                                    <flux:tooltip content="Delete Strand" position="top">
                                                        <flux:icon.trash 
                                                            x-show="!isHovered" 
                                                            variant="outline" 
                                                            class="text-red-500 cursor-pointer ml-5" 
                                                        />
                                                    </flux:tooltip>

                                                    <flux:tooltip content="Delete Strand" position="top">
                                                        <flux:icon.trash 
                                                            x-show="isHovered" 
                                                            variant="solid" 
                                                            class="text-red-500 cursor-pointer ml-5" 
                                                            x-cloak 
                                                            wire:click="$dispatch('open-delete-strand-modal', { id: {{ $strand->id }} })"
                                                        />
                                                    </flux:tooltip>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
