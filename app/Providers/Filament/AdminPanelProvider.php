<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\CheckStaffShift;
use App\Http\Middleware\CurrentCashier;
use App\Filament\Widgets\StaffShiftWidget;
use App\Filament\Pages\Login;
use App\Filament\Widgets\AccountWidget;
use Filament\Navigation\MenuItem;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use App\Filament\Resources;
use App\Filament\Pages as AdminPages;
use Njxqlus\FilamentProgressbar\FilamentProgressbarPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                StaffShiftWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                CheckStaffShift::class,
                CurrentCashier::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentFullCalendarPlugin::make(),
                FilamentProgressbarPlugin::make()->color('#29b'),
            ])
            ->brandName('MINDSPACE')
            ->userMenuItems([
                'logout' => MenuItem::make()->label('Log out'),
                // ...
            ])
            ->favicon('https://ik.imagekit.io/wow2navhj/Mindspace/mindspace_logo_circle.png?updatedAt=1716357911454')
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder
                    ->groups([
                        NavigationGroup::make()
                            ->label('')
                            ->collapsible(false)
                            ->items([
                                NavigationItem::make()
                                    ->label('Dashboard')
                                    ->icon('heroicon-o-home')
                                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.dashboard'))
                                    ->url(route('filament.admin.pages.dashboard')),
                                NavigationItem::make()
                                    ->label('Cash Logs')
                                    ->icon('heroicon-o-banknotes')
                                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.resources.cash-logs.index'))
                                    ->url(route('filament.admin.resources.cash-logs.index')),
                            ]),
                        NavigationGroup::make()
                            ->label('SALES')
                            ->collapsible(false)
                            ->items([
                                ...Resources\DailySaleResource::getNavigationItems(),
                                ...Resources\FlexiUserResource::getNavigationItems(),
                                ...Resources\MonthlyUserResource::getNavigationItems(),
                                ...Resources\ConferenceResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make()
                            ->label('STAFF')
                            ->collapsible(false)
                            ->items([
                                ...Resources\ReportResource::getNavigationItems(),
                                ...Resources\AttendanceResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make()
                            ->label('ADMIN')
                            ->collapsible(false)
                            ->items([
                                ...Resources\UserResource::getNavigationItems(),
                                ...Resources\CardResource::getNavigationItems(),
                                ...Resources\StaffResource::getNavigationItems(),
                                NavigationItem::make()
                                    ->label('Rates')
                                    ->icon('heroicon-o-information-circle')
                                    ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.rate'))
                                    ->url(route('filament.admin.pages.rate'))
                                    ->visible(auth()->user()->hasRole('Super Administrator')),
                                // NavigationItem::make()
                                //     ->label('Settings')
                                //     ->icon('heroicon-o-adjustments-horizontal')
                                //     ->isActiveWhen(fn () => request()->routeIs('filament.admin.pages.setting'))
                                //     ->url(route('filament.admin.pages.setting'))
                                //     ->visible(auth()->user()->hasRole('Super Administrator')),
                                ...Resources\InventoryResource::getNavigationItems(),
                                ...Resources\ActivityLogResource::getNavigationItems(),
                                ...Resources\ErrorLogResource::getNavigationItems(),
                                ...Resources\ExpenseResource::getNavigationItems(),
                                ...Resources\MaintenanceResource::getNavigationItems(),
                                ...Resources\RoleResource::getNavigationItems(),
                                ...Resources\PermissionResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make()
                            ->label('REPORTS')
                            ->collapsible()
                            ->items([
                                ...Resources\DailySalesReportResource::getNavigationItems(),
                                ...Resources\MonthlySalesReportResource::getNavigationItems(),
                            ]),
                        NavigationGroup::make()
                            ->label('')
                            ->collapsible(false)
                            ->items([
                                NavigationItem::make()
                                    ->label('Logout')
                                    ->icon('heroicon-o-arrow-right-start-on-rectangle')
                                    ->url('/logout'),
                            ]),
                    ]);
            });
    }
}
