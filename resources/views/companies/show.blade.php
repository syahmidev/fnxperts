<x-layouts::app :title="$company->name">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-1">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('companies.index')" wire:navigate>{{ __('Companies') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ $company->name }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
        </div>

        <x-flash />

        <div class="flex flex-col gap-4 rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                @if ($company->logo)
                    <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}"
                         class="size-20 rounded-xl border border-neutral-200 object-cover dark:border-neutral-700">
                @else
                    <div class="flex size-20 items-center justify-center rounded-xl bg-neutral-100 text-neutral-400 dark:bg-neutral-700">
                        <flux:icon.building-office-2 class="size-8" />
                    </div>
                @endif
                <div>
                    <flux:heading size="xl">{{ $company->name }}</flux:heading>
                    <flux:text>{{ $company->employees_count }} {{ __('employees') }}</flux:text>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <flux:button :href="route('companies.edit', $company)" variant="primary" icon="pencil-square">{{ __('Edit') }}</flux:button>
                <form method="POST" action="{{ route('companies.destroy', $company) }}"
                      onsubmit="return confirm('{{ __('Delete this company? Its employees will also be removed.') }}')">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" variant="danger" icon="trash">{{ __('Delete') }}</flux:button>
                </form>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:text class="text-xs uppercase tracking-wide text-neutral-500">{{ __('Email') }}</flux:text>
                <flux:text class="font-medium">{{ $company->email ?? '—' }}</flux:text>
            </div>
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:text class="text-xs uppercase tracking-wide text-neutral-500">{{ __('Website') }}</flux:text>
                @if ($company->website)
                    <a href="{{ $company->website }}" target="_blank" rel="noopener"
                       class="font-medium text-blue-600 hover:underline dark:text-blue-400">{{ $company->website }}</a>
                @else
                    <flux:text class="font-medium">—</flux:text>
                @endif
            </div>
        </div>

        {{-- Employees --}}
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('Employees') }}</flux:heading>
            <flux:button :href="route('employees.create')" size="sm" variant="ghost" icon="plus">{{ __('Add Employee') }}</flux:button>
        </div>

        @if ($company->employees->isEmpty())
            <div class="rounded-xl border border-dashed border-neutral-300 px-6 py-10 text-center dark:border-neutral-700">
                <flux:text>{{ __('This company has no employees yet.') }}</flux:text>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Email') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Phone') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach ($company->employees as $employee)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50">
                                <td class="px-4 py-3 font-medium">
                                    <flux:link :href="route('employees.show', $employee)" wire:navigate>{{ $employee->full_name }}</flux:link>
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $employee->email ?? '—' }}</td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $employee->phone ?? '—' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <flux:button :href="route('employees.edit', $employee)" size="sm" variant="ghost" icon="pencil-square" inset="top bottom" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts::app>
