<x-layouts::app :title="__('Trashed Employees')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-1">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('employees.index')" wire:navigate>{{ __('Employees') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Trashed') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <div class="flex items-center justify-between">
                <flux:heading size="xl">{{ __('Trashed Employees') }}</flux:heading>
                <flux:button :href="route('employees.index')" variant="ghost" icon="arrow-left">{{ __('Back to Employees') }}</flux:button>
            </div>
        </div>

        <x-flash />

        @if ($employees->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-neutral-300 py-16 dark:border-neutral-700">
                <flux:icon.trash class="size-10 text-neutral-400" />
                <flux:heading size="lg">{{ __('Trash is empty') }}</flux:heading>
                <flux:text>{{ __('Deleted employees will appear here and can be restored.') }}</flux:text>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Company') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Email') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Deleted') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach ($employees as $employee)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50">
                                <td class="px-4 py-3 font-medium">{{ $employee->full_name }}</td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $employee->company?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $employee->email ?? '—' }}</td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $employee->deleted_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('employees.restore', $employee) }}">
                                            @csrf
                                            @method('PUT')
                                            <flux:button type="submit" size="sm" variant="primary" icon="arrow-uturn-left">{{ __('Restore') }}</flux:button>
                                        </form>
                                        <form method="POST" action="{{ route('employees.force-destroy', $employee) }}"
                                              onsubmit="return confirm('{{ __('Permanently delete this employee? This cannot be undone.') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button type="submit" size="sm" variant="danger" icon="trash">{{ __('Delete Forever') }}</flux:button>
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
