<?php

namespace App\Filament\Resources\FlexiUserResource\Pages;

use App\Filament\Resources\FlexiUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\FlexiUser;
use App\Models\Rate;
use Excel;
use Filament\Forms\Components as FormComponents;
use Filament\Support\Enums\MaxWidth;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ListFlexiUsers extends ListRecords
{
    protected static string $resource = FlexiUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export-creators')
                ->label('Export')
                ->modalWidth(MaxWidth::Large)
                ->modalHeading('Export Creators')
                ->fillForm(function ($data) {
                    $data['user'] = 'all';
                    $data['status'] = 'active';
                    $data['package'] = 'all';

                    return $data;
                })
                ->form([
                    FormComponents\Grid::make(1)
                        ->schema([
                            FormComponents\Select::make('user')
                                ->label('Flexi User')
                                ->options(function() {
                                    $options = ['all' => 'All'];
                                    $names = \DB::table('flexi_users')
                                        ->select('name')
                                        ->distinct()
                                        ->pluck('name');
                                    foreach($names as $name) {
                                        $options[$name] = $name;
                                    }

                                    return $options;
                                })
                                ->native(false)
                                ->required(),
                            FormComponents\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'all' => 'All',
                                    'active' => 'Active',
                                    'expired' => 'Expired'
                                ])
                                ->native(false)
                                ->required(),
                            FormComponents\Select::make('package')
                                ->label('Package')
                                ->options(function() {
                                    $options = ['all' => 'All', 'old' => 'Old Pass'];
                                    $packages = Rate::where('type', 'Flexi')->get();
                                    foreach($packages as $package) {
                                        $options[$package->id] = $package->name;
                                    }

                                    return $options;
                                })
                                ->native(false)
                                ->required(),
                        ])
                        ->columnSpanFull()
                ])
                ->action(function ($data, $livewire) {
                    $coaches = FlexiUser::query()
                        ->with('rate')
                        ->when($data['user'] !== 'all', function ($query) use ($data) {
                            $query->where('name', $data['user']);
                        })
                        ->when($data['status'] !== 'all', function ($query) use ($data) {
                            if ($data['status'] === 'active') {
                                $query->where('status', true);
                            } else {
                                $query->where('status', false);
                            }
                        })
                        ->when($data['package'] !== 'all', function ($query) use ($data) {
                            if ($data['package'] === 'old') {
                                $query->whereNull('rate_id');
                            } else {
                                $query->where('rate_id', $data['package']);
                            }
                        })
                        ->get();

                    $export = [];
                    $headings = [
                        'ID',
                        'Package',
                        'Name',
                        'Contact No',
                        'Status',
                        'Date Start',
                        'Date Expired',
                        'Remaining TIme'
                    ];

                    foreach ($coaches as $item) {
                        $exportData = [
                            'ID' => $item->id,
                            'Package' => $item->rate?->name,
                            'Name' => $item->name,
                            'Contact No' => $item->contact_no,
                            'Status' => $item->status ? 'Active' : 'Expired',
                            'Date Start' => Carbon::parse($item->start_at)->format(config('app.date_time_format')),
                            'Date Expired' => Carbon::parse($item->expired_at)->format(config('app.date_time_format')),
                            'Remaining Time' => $item->remaining_time
                        ];

                        $export[] = $exportData;
                    }
                    $exportFile = new \App\Exports\ExportModule($headings, $export);

                    Notification::make()
                        ->title('Success')
                        ->body('Flexi users successfully exported.')
                        ->success()
                        ->send();

                    return Excel::download($exportFile, 'flexi-users-' . date('Y-m-d') . '.csv');
                })
                ->visible(auth()->user()->hasRole('Super Administrator'))
                ->icon('heroicon-o-arrow-up-on-square'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', true))
                ->badge(FlexiUser::query()->where('status', true)->count()),
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', false))
                ->badge(FlexiUser::query()->where('status', false)->count()),
            'all' => Tab::make('All')
                ->badge(FlexiUser::query()->count()),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            
        ];
    }
}
