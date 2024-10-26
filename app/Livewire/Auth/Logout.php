<?php

namespace App\Livewire\Auth;

use Livewire\Component;

class Logout extends Component
{
    public function render()
    {
        return view('livewire.auth.logout');
    }

    public function logout()
    {
        auth()->logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('login'));
    }
}
