<div>
    <form wire:submit="create">
        {{ $this->form }}

        <div wire:poll.1s="getSessionCard">
            {{ dd(session()->all()) }}
            @if($rfid)
                <p>Session Card: {{ $rfid }}</p>
            @else
                <p>No card in session.</p>
            @endif
        </div>
        
        <button type="submit">
            Submit
        </button>
    </form>
    
    <x-filament-actions::modals />
</div>