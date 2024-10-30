<?php

namespace App\Livewire\Auth\Password;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Reset extends Component
{
    public ?string $token = null;

    public ?string $email = null;

    public function mount():void
    {
        $this->token = request('token');

        if ($this->tokenNotValid()) {
            session()->flash('status', 'Token Invalid');
            $this->redirectRoute('login');
        }
    }

    public function render()
    {
        return view('livewire.auth.password.reset');
    }

    private function tokenNotValid():bool
    {
        $tokens = DB::table('password_reset_tokens')
            ->get(['token']);

        foreach ($tokens as $t) {
            if (Hash::check($this->token, $t->token)) {
                return false;
            }
        }

        return true;
    }
}
