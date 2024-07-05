<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components as FormComponents;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'rfid',
        'type',
    ];

    public static function getTypeSelectOptions()
    {
        return [
            'Staff' => 'Staff',
            'Daily' => 'Daily',
            'Monthly' => 'Monthly',
        ];
    }

    public static function getForm()
    {
        return [
            FormComponents\Grid::make(1)
                ->schema([
                    FormComponents\TextInput::make('code')
                        ->required(),
                    FormComponents\TextInput::make('rfid'),
                    FormComponents\Select::make('type')
                        ->options(self::getTypeSelectOptions())
                        ->searchable()
                        ->required()
                        ->native(false),
                ])
        ];
    }
}
