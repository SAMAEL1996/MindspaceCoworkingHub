<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;
use Filament\Forms\Components as FormComponents;
use App\Models\Permission;

class Role extends SpatieRole
{
    use HasFactory;

    public static function getForm()
    {
        return [
            FormComponents\Grid::make(1)
                ->schema([
                    FormComponents\TextInput::make('name')
                        ->required(function($record) {
                            if(!$record) {
                                return false;
                            }
                            return $record ? false : true;
                        })
                        ->unique(function($record) {
                            return $record ? false : true;
                        })
                        ->disabled(function($record) {
                            return $record ? true : false;
                        }),
                    FormComponents\Select::make('permissions')
                        ->native(false)
                        ->multiple()
                        ->searchable()
                        ->options(Permission::all()->pluck('name', 'id'))
                ])
        ];
    }
}
