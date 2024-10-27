<x-card title="Password Recovery" shadow class="mx-auto w-[450px]">

    @if($message)
        <x-alert icon="o-exclamation-triangle" class="alert-warning mb-4" >
                <span>You will receive an email with the password recovery link.</span>
        </x-alert>
    @endif

    <x-form wire:submit="startPasswordRecovery">
        <x-input label="Email" wire:model="email" />

        <x-slot:actions>
            <div class="w-full flex items-center justify-between">
                <a wire:navigate href="{{route('auth.login')}}" class="link link-primary">
                    Never mind, get back to login page.
                </a>
                <div>
                    <x-button label="Submit" class="btn-primary" type="submit" spinner="submit" />
                </div>
            </div>
        </x-slot:actions>
    </x-form>
</x-card>
