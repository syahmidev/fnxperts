<x-layouts::app :title="__('Employees')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Employees') }}</flux:heading>
            <div class="flex items-center gap-2">
                <flux:button :href="route('employees.trashed')" variant="ghost" icon="trash">
                    {{ __('Trashed') }}
                </flux:button>
                <flux:button :href="route('employees.create')" variant="primary" icon="plus">
                    {{ __('New Employee') }}
                </flux:button>
            </div>
        </div>

        <x-flash />

        @if ($employees->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-neutral-300 py-16 dark:border-neutral-700">
                <flux:icon.users class="size-10 text-neutral-400" />
                <flux:heading size="lg">{{ __('No employees yet') }}</flux:heading>
                <flux:text>{{ __('Get started by adding your first employee.') }}</flux:text>
                <flux:button :href="route('employees.create')" variant="primary" class="mt-2">
                    {{ __('New Employee') }}
                </flux:button>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Company') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Email') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Phone') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Created') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach ($employees as $employee)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50">
                                <td class="px-4 py-3 font-medium">
                                    <flux:link :href="route('employees.show', $employee)" wire:navigate>{{ $employee->full_name }}</flux:link>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($employee->company)
                                        <flux:link :href="route('companies.show', $employee->company)" wire:navigate class="text-neutral-600 dark:text-neutral-400">
                                            {{ $employee->company->name }}
                                        </flux:link>
                                    @else
                                        <span class="text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $employee->email ?? '—' }}</td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $employee->phone ?? '—' }}</td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $employee->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:button :href="route('employees.show', $employee)" size="sm" variant="ghost" icon="eye" inset="top bottom" />
                                        <flux:button :href="route('employees.edit', $employee)" size="sm" variant="ghost" icon="pencil-square" inset="top bottom" />
                                        <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                                              onsubmit="return confirm('{{ __('Delete this employee?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="ghost" icon="trash" inset="top bottom" class="text-red-600 dark:text-red-400" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>{{ $employees->onEachSide(1)->links() }}</div>
        @endif
    </div>
</x-layouts::app>
