<?php

namespace App\Livewire;

use Livewire\Component;

class AudioNotifier extends Component
{
    public int $previousCount = 0;

    public function mount()
    {
        if (auth()->check()) {
            $this->previousCount = auth()->user()->unreadNotifications()->count();
        }
    }

    public function checkNotifications()
    {
        if (!auth()->check()) {
            return;
        }

        $currentCount = auth()->user()->unreadNotifications()->count();

        // If the number of unread notifications increased, play sound
        if ($currentCount > $this->previousCount) {
            $this->dispatch('play-audio');
        }

        $this->previousCount = $currentCount;
    }

    public function render()
    {
        return view('livewire.audio-notifier');
    }
}
