<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Staff;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-cog';
    protected static ?string $navigationGroup = 'STAFF PROFILE';

    protected static string $view = 'filament.pages.profile';

    public $user;

    public function mount(): void
	{
        if(request()->has('record')) {
            $this->user = Staff::where('uid', request('record'))->first()->user;
        } else {
            $this->user = auth()->user();
        }
	}
}
