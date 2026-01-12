<div class="">
    {{-- App Header --}}
    <div class="flex items-center justify-between mb-10">

        @php

            $route = request()->route()->getName();

            $output = match (true) {
                $route == 'admin_dashboard' => 'Dashboard',
                $route == 'student_list' => 'Student List',
                $route ==  'student_logs' => 'Student Logs',
                $route == 'about_kiroku' => 'About Kiroku SLS',               
                
                default => 'Breadcrumb',
            };

        @endphp
        

        {{-- Breadcrumbs --}}
        <x-ui.breadcrumbs>
            <x-ui.breadcrumbs.item>Kiroku</x-ui.breadcrumbs.item>
            <x-ui.breadcrumbs.item wire:navigate>{{ $output }}</x-ui.breadcrumbs.item>
        </x-ui.breadcrumbs>

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
</div>