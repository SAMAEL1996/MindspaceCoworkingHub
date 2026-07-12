<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Card;
use App\Models\CashLog;
use App\Models\CashLogItem;
use App\Models\Conference;
use App\Models\ConferenceMember;
use App\Models\DailySale;
use App\Models\ErrorLog;
use App\Models\Expense;
use App\Models\FlexiUser;
use App\Models\Guest;
use App\Models\Inventory;
use App\Models\LoyaltyCard;
use App\Models\Maintenance;
use App\Models\MonthlyUser;
use App\Models\Permission;
use App\Models\Profile;
use App\Models\Rate;
use App\Models\Report;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleReport;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\User;
use App\Models\UserLocation;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FactorySeeder extends Seeder
{
    /**
     * Seed the application's database with factory-driven demo data.
     */
    public function run(): void
    {
        $permissions = Permission::factory()->count(12)->create();
        $roles = Role::factory()->count(3)->create();

        $roles->each(function (Role $role) use ($permissions): void {
            $permissionCount = fake()->numberBetween(2, min(5, $permissions->count()));

            $role->syncPermissions(
                $permissions->random($permissionCount)->pluck('name')->all()
            );
        });

        $users = User::factory()->count(8)->create();

        $staffCards = Card::factory()->count(4)->staff()->create();
        $dailyCards = Card::factory()->count(14)->daily()->create();
        $monthlyCards = Card::factory()->count(4)->monthly()->create();

        $staffUsers = User::factory()->count($staffCards->count())->create([
            'is_staff' => true,
            'status' => true,
        ]);

        $staffMembers = Staff::factory()
            ->count($staffUsers->count())
            ->state(new Sequence(
                ...$staffUsers->values()->map(fn (User $user, int $index): array => [
                    'user_id' => $user->id,
                    'card_id' => $staffCards[$index]->id,
                ])->all()
            ))
            ->create();

        $profileUsers = User::factory()->count(2)->create([
            'is_staff' => true,
            'status' => true,
        ]);
        $profileCards = Card::factory()->count($profileUsers->count())->staff()->create();

        $profileStaff = Staff::withoutEvents(function () use ($profileUsers, $profileCards) {
            return Staff::factory()
                ->count($profileUsers->count())
                ->state(new Sequence(
                    ...$profileUsers->values()->map(fn (User $user, int $index): array => [
                        'user_id' => $user->id,
                        'card_id' => $profileCards[$index]->id,
                    ])->all()
                ))
                ->create();
        });

        Profile::factory()
            ->count($profileStaff->count())
            ->state(new Sequence(
                ...$profileStaff->values()->map(fn (Staff $staff): array => [
                    'staff_id' => $staff->id,
                ])->all()
            ))
            ->create();

        $allUsers = $users
            ->concat($staffUsers)
            ->concat($profileUsers)
            ->values();

        $allStaff = $staffMembers
            ->concat($profileStaff)
            ->values();

        UserLocation::factory()
            ->count($allUsers->count())
            ->state(new Sequence(
                ...$allUsers->values()->map(fn (User $user): array => [
                    'user_id' => $user->id,
                ])->all()
            ))
            ->create();

        $roles->values()->each(function (Role $role, int $index) use ($allUsers): void {
            $user = $allUsers->get($index);

            if ($user) {
                $user->assignRole($role);
            }
        });

        collect([
            ['key' => 'conference-package-1-additional-person', 'value' => '300'],
            ['key' => 'conference-package-1-succeeding-hours', 'value' => '250'],
            ['key' => 'conference-package-2-succeeding-hours', 'value' => '300'],
        ])->each(fn (array $setting): Setting => Setting::factory()->create($setting));

        Setting::factory()->count(3)->create();

        collect([1, 5, 8, 24])->each(
            fn (int $hours): Rate => Rate::factory()->daily($hours)->create()
        );

        $flexiRates = collect([50, 100])->map(
            fn (int $hours): Rate => Rate::factory()->flexi($hours)->create()
        )->values();

        $monthlyRate = Rate::factory()->monthly()->create();

        collect([1, 2])->each(function (int $packageId): void {
            collect([3, 5, 8, 24])->each(
                fn (int $hours): Rate => Rate::factory()->conference($packageId, $hours)->create()
            );
        });

        Activity::factory()
            ->count(6)
            ->state(fn (): array => [
                'causer_id' => $allUsers->random()->id,
            ])
            ->create();

        Guest::factory()->count(5)->create();
        LoyaltyCard::factory()->count(6)->create();
        Maintenance::factory()->count(4)->create();
        Expense::factory()->count(5)->create();

        Inventory::factory()
            ->count(6)
            ->state(fn (): array => [
                'user_id' => $allUsers->random()->id,
            ])
            ->create();

        $cashLogs = CashLog::factory()
            ->count(4)
            ->state(fn (): array => [
                'user_id' => $allUsers->random()->id,
            ])
            ->create();

        CashLogItem::factory()
            ->count(10)
            ->state(fn (): array => [
                'cash_log_id' => $cashLogs->random()->id,
            ])
            ->create();

        $attendances = Attendance::factory()
            ->count(8)
            ->state(fn (): array => [
                'staff_id' => $allStaff->random()->id,
            ])
            ->create();

        $dailySales = DailySale::factory()
            ->count(10)
            ->state(fn (): array => [
                'card_id' => $dailyCards->random()->id,
                'time_in_staff_id' => $allStaff->random()->id,
                'time_out_staff_id' => $allStaff->random()->id,
            ])
            ->create();

        Sale::factory()->count(4)->create();

        MonthlyUser::factory()
            ->count(3)
            ->state(new Sequence(
                ...$monthlyCards->take(3)->values()->map(fn (Card $card): array => [
                    'rate_id' => $monthlyRate->id,
                    'card_id' => $card->id,
                    'amount' => $monthlyRate->price,
                ])->all()
            ))
            ->create();

        $flexiBlueprints = $dailyCards
            ->take(4)
            ->values()
            ->map(function (Card $card, int $index) use ($flexiRates): array {
                $rate = $flexiRates[$index % $flexiRates->count()];
                $startAt = Carbon::now()->subDays($index + 1)->startOfHour();
                $endAt = $startAt->copy()->addHours((int) $rate->consumable);

                return [
                    'rate_id' => $rate->id,
                    'card_id' => $card->id,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'expired_at' => $startAt->copy()->addDays((int) $rate->validity),
                    'amount' => (int) $rate->price,
                    'remaining' => $startAt->diffInMinutes($endAt),
                ];
            });

        FlexiUser::factory()
            ->count($flexiBlueprints->count())
            ->state(new Sequence(...$flexiBlueprints->all()))
            ->create();

        $conferences = Conference::factory()
            ->count(4)
            ->state(fn (): array => [
                'book_by' => $allUsers->random()->id,
            ])
            ->create();

        ConferenceMember::factory()
            ->count(8)
            ->state(fn (): array => [
                'conference_id' => $conferences->random()->id,
                'card_id' => $dailyCards->random()->id,
            ])
            ->create();

        ErrorLog::factory()
            ->count(4)
            ->state(fn (): array => [
                'staff_id' => $allStaff->random()->id,
            ])
            ->create();

        Report::factory()
            ->count(4)
            ->state(fn (): array => [
                'staff_id' => $allStaff->random()->id,
                'attendance_id' => $attendances->random()->id,
            ])
            ->create();

        SaleReport::factory()
            ->count(4)
            ->state(fn (): array => [
                'staff_id' => $allStaff->random()->id,
                'daily_sale_id' => $dailySales->random()->id,
            ])
            ->create();
    }
}
