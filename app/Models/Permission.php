<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Filament\Forms\Components as FormComponents;

class Permission extends SpatiePermission
{
    use HasFactory;

    public static function getForm()
    {
        return [
            FormComponents\Grid::make(1)
                ->schema([
                    FormComponents\TextInput::make('name')
                        ->required()
                        ->unique(),
                    FormComponents\Textarea::make('description')
                        ->rows(5),
                ])
        ];
    }
}
