<div class="flex flex-col gap-6">
    <x-auth-header title="Log in to your account" description="Enter your username and password below to log in" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="login" class="flex flex-col gap-6">
        @csrf

        <!-- Username -->
        <flux:input
            wire:model.defer="username"
            name="username"
            label="Username"
            type="text"
            required
            autofocus
            placeholder="Enter Username"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model.defer="password"
                name="password"
                label="Password"
                type="password"
                required
                placeholder="Password"
                viewable
            />
        </div>

        <!-- Remember Me -->
        {{-- <flux:checkbox wire:model="remember" name="remember" label="Remember me" :checked="old('remember')" /> --}}

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full" >
                Log in
            </flux:button>
        </div>
    </form>
</div>
