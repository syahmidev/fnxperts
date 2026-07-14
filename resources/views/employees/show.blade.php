<x-layouts::app :title="$employee->full_name">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-1">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('employees.index')" wire:navigate>{{ __('Employees') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $employee->full_name }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-flash />

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <flux:heading size="xl">{{ $employee->full_name }}</flux:heading>
                @if ($employee->company)
                    <flux:text>
                        {{ __('at') }}
                        <flux:link :href="route('companies.show', $employee->company)" wire:navigate>{{ $employee->company->name }}</flux:link>
                    </flux:text>
                @endif
            </div>
            <div class="flex items-center gap-2">
                <flux:button :href="route('employees.edit', $employee)" variant="primary" icon="pencil-square">{{ __('Edit') }}</flux:button>
                <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                      onsubmit="return confirm('{{ __('Delete this employee?') }}')">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="danger" icon="trash">{{ __('Delete') }}</flux:button>
                </form>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:text class="text-xs uppercase tracking-wide text-neutral-500">{{ __('Company') }}</flux:text>
                <flux:text class="font-medium">{{ $employee->company?->name ?? '—' }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:text class="text-xs uppercase tracking-wide text-neutral-500">{{ __('Email') }}</flux:text>
                <flux:text class="font-medium">{{ $employee->email ?? '—' }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:text class="text-xs uppercase tracking-wide text-neutral-500">{{ __('Phone') }}</flux:text>
                <flux:text class="font-medium">{{ $employee->phone ?? '—' }}</flux:text>
            </div>
        </div>
    </div>
</x-layouts::app>
