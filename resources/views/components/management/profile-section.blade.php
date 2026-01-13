<div class="">

    {{-- Profile Section --}}
    <div class="flex items-center gap-2">
        <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()" :chevron="false" />

        <flux:separator vertical class="my-2" />

        <div class="flex items-center gap-2">

            {{-- <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" aria-label="Toggle dark mode" /> --}}
            
            {{-- Log Out Button --}}
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:button type="submit" icon="arrow-right-start-on-rectangle" variant="ghost">
                    Log Out
                </flux:button>
            </form>
        </div>
    </div>

</div>