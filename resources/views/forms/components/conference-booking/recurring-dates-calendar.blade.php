<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.$entangle('{{ $getStatePath() }}').live,
            currentMonth: new Date(new Date().getFullYear(), new Date().getMonth(), 1),
            dayLabels: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            get dates() {
                return Array.isArray(this.state) ? this.state : [];
            },
            init() {
                if (! Array.isArray(this.state)) {
                    this.state = [];
                }
            },
            monthLabel() {
                return this.currentMonth.toLocaleDateString('en-PH', {
                    month: 'long',
                    year: 'numeric',
                });
            },
            startOfToday() {
                const today = new Date();
                return new Date(today.getFullYear(), today.getMonth(), today.getDate());
            },
            previousMonth() {
                this.currentMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() - 1, 1);
            },
            nextMonth() {
                this.currentMonth = new Date(this.currentMonth.getFullYear(), this.currentMonth.getMonth() + 1, 1);
            },
            days() {
                const year = this.currentMonth.getFullYear();
                const month = this.currentMonth.getMonth();
                const firstDayOfMonth = new Date(year, month, 1);
                const lastDayOfMonth = new Date(year, month + 1, 0);
                const leadingBlanks = firstDayOfMonth.getDay();
                const totalDays = lastDayOfMonth.getDate();
                const items = [];

                for (let index = 0; index < leadingBlanks; index++) {
                    items.push({ empty: true, key: `blank-${year}-${month}-${index}` });
                }

                for (let day = 1; day <= totalDays; day++) {
                    const date = new Date(year, month, day);
                    items.push({
                        empty: false,
                        key: this.toIso(date),
                        label: day,
                        iso: this.toIso(date),
                        isPast: date < this.startOfToday(),
                    });
                }

                return items;
            },
            toIso(date) {
                const year = date.getFullYear();
                const month = `${date.getMonth() + 1}`.padStart(2, '0');
                const day = `${date.getDate()}`.padStart(2, '0');

                return `${year}-${month}-${day}`;
            },
            isSelected(iso) {
                return this.dates.includes(iso);
            },
            toggleDate(iso) {
                if (this.isSelected(iso)) {
                    this.state = this.dates.filter((date) => date !== iso);
                    return;
                }

                this.state = [...this.dates, iso].sort();
            },
            formatDate(date) {
                return new Date(`${date}T00:00:00`).toLocaleDateString('en-PH', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                });
            },
        }"
        class="space-y-4"
    >
        <div class="flex items-center justify-between gap-3">
            <x-filament::button type="button" color="gray" outlined x-on:click="previousMonth()">
                Prev
            </x-filament::button>

            <div class="text-sm font-semibold text-gray-950 dark:text-white" x-text="monthLabel()"></div>

            <x-filament::button type="button" color="gray" outlined x-on:click="nextMonth()">
                Next
            </x-filament::button>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
            <template x-for="label in dayLabels" :key="label">
                <div x-text="label"></div>
            </template>
        </div>

        <div class="grid grid-cols-7 gap-2">
            <template x-for="day in days()" :key="day.key">
                <div>
                    <template x-if="day.empty">
                        <div class="h-10 rounded-lg"></div>
                    </template>

                    <template x-if="! day.empty">
                        <button
                            type="button"
                            class="h-10 w-full rounded-lg border text-sm font-medium transition"
                            :class="day.isPast
                                ? 'cursor-not-allowed border-gray-200 bg-gray-50 text-gray-300 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-600'
                                : isSelected(day.iso)
                                    ? 'border-primary-600 bg-primary-600 text-white shadow-sm'
                                    : 'border-gray-200 bg-white text-gray-700 hover:border-primary-400 hover:text-primary-600 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200'"
                            :disabled="day.isPast"
                            x-on:click="toggleDate(day.iso)"
                            x-text="day.label"
                        ></button>
                    </template>
                </div>
            </template>
        </div>

        <div class="text-sm text-gray-500 dark:text-gray-400">
            Pick every date that should use the same time and duration.
        </div>

        <template x-if="dates.length">
            <div class="flex flex-wrap gap-2">
                <template x-for="date in dates" :key="date">
                    <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/10 dark:bg-primary-500/10 dark:text-primary-300">
                        <span x-text="formatDate(date)"></span>
                    </span>
                </template>
            </div>
        </template>
    </div>
</x-dynamic-component>
