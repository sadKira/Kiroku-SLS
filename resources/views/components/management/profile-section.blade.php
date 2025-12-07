<div class="flex items-center gap-3">

    {{-- User profile --}}
    <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()" :chevron="false" />

    <flux:separator vertical />

    {{-- Log out button --}}
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <flux:button type="submit" icon="arrow-right-start-on-rectangle" variant="ghost">
            Log Out
        </flux:button>
    </form>

</div>
