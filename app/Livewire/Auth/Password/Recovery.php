<?php

namespace App\Livewire\Auth\Password;

use App\Models\User;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Recovery extends Component
{
    public ?string $message = null;

    #[Rule(['required', 'email'])]
    public ?string $email = null;


    public function render()
    {
        return view('livewire.auth.password.recovery')
            ->layout('components.layouts.guest');
    }

    public function startPasswordRecovery(): void
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();
        Password::sendResetLink($this->only('email'));

        $this->message = 'You will receive an email with the password recovery link.';
    }
}
