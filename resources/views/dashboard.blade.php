<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>

        {{-- Summary cards --}}
        <div class="grid auto-rows-min gap-4 md:grid-cols-2">
            <a href="{{ route('companies.index') }}" wire:navigate
               class="flex items-center gap-4 rounded-xl border border-neutral-200 p-6 transition hover:border-neutral-300 dark:border-neutral-700 dark:hover:border-neutral-600">
                <div class="flex size-12 items-center justify-center rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
                    <flux:icon.building-office-2 class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm">{{ __('Total Companies') }}</flux:text>
                    <flux:heading size="xl">{{ $companiesCount }}</flux:heading>
                </div>
            </a>

            <a href="{{ route('employees.index') }}" wire:navigate
               class="flex items-center gap-4 rounded-xl border border-neutral-200 p-6 transition hover:border-neutral-300 dark:border-neutral-700 dark:hover:border-neutral-600">
                <div class="flex size-12 items-center justify-center rounded-lg bg-green-100 text-green-600 dark:bg-green-500/20 dark:text-green-400">
                    <flux:icon.users class="size-6" />
                </div>
                <div>
                    <flux:text class="text-sm">{{ __('Total Employees') }}</flux:text>
                    <flux:heading size="xl">{{ $employeesCount }}</flux:heading>
                </div>
            </a>
        </div>

        {{-- Recent lists --}}
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <flux:heading size="lg">{{ __('Recent Companies') }}</flux:heading>
                    <flux:link :href="route('companies.index')" wire:navigate>{{ __('View all') }}</flux:link>
                </div>
                <ul class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($recentCompanies as $company)
                        <li class="flex items-center justify-between px-6 py-3">
                            <flux:link :href="route('companies.show', $company)" wire:navigate class="font-medium">
                                {{ $company->name }}
                            </flux:link>
                            <flux:badge size="sm" color="zinc">{{ $company->employees_count }} {{ __('employees') }}</flux:badge>
                        </li>
                    @empty
                        <li class="px-6 py-4"><flux:text>{{ __('No companies yet.') }}</flux:text></li>
                    @endforelse
                </ul>
            </div>

            <div class="rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="flex items-center justify-between border-b border-neutral-200 px-6 py-4 dark:border-neutral-700">
                    <flux:heading size="lg">{{ __('Recent Employees') }}</flux:heading>
                    <flux:link :href="route('employees.index')" wire:navigate>{{ __('View all') }}</flux:link>
                </div>
                <ul class="divide-y divide-neutral-200 dark:divide-neutral-700">
                    @forelse ($recentEmployees as $employee)
                        <li class="flex items-center justify-between px-6 py-3">
                            <flux:link :href="route('employees.show', $employee)" wire:navigate class="font-medium">
                                {{ $employee->full_name }}
                            </flux:link>
                            <flux:text class="text-sm">{{ $employee->company?->name }}</flux:text>
                        </li>
                    @empty
                        <li class="px-6 py-4"><flux:text>{{ __('No employees yet.') }}</flux:text></li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-layouts::app>
