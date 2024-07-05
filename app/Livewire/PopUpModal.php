<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;

class PopUpModal extends Component
{
    public User $user;

    public $showModal = false;
    
    public function mount() {
        if(!session()->has('staff-access')) {
            $this->dispatch('open-modal', 'shiftModal');
        }
    }

    public function render()
    {
        return view('livewire.pop-up-modal');
    }
}
