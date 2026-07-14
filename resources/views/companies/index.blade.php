<x-layouts::app :title="__('Companies')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <flux:heading size="xl">{{ __('Companies') }}</flux:heading>
            <div class="flex items-center gap-2">
                <flux:button :href="route('companies.trashed')" variant="ghost" icon="trash">
                    {{ __('Trashed') }}
                </flux:button>
                <flux:button :href="route('companies.create')" variant="primary" icon="plus">
                    {{ __('New Company') }}
                </flux:button>
            </div>
        </div>

        <x-flash />

        @if ($companies->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 rounded-xl border border-dashed border-neutral-300 py-16 dark:border-neutral-700">
                <flux:icon.building-office-2 class="size-10 text-neutral-400" />
                <flux:heading size="lg">{{ __('No companies yet') }}</flux:heading>
                <flux:text>{{ __('Get started by creating your first company.') }}</flux:text>
                <flux:button :href="route('companies.create')" variant="primary" class="mt-2">
                    {{ __('New Company') }}
                </flux:button>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-neutral-200 dark:border-neutral-700">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-neutral-200 bg-neutral-50 text-neutral-600 dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-300">
                        <tr>
                            <th class="px-4 py-3 font-medium">{{ __('Logo') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Name') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Email') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Website') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Employees') }}</th>
                            <th class="px-4 py-3 font-medium">{{ __('Created') }}</th>
                            <th class="px-4 py-3 text-right font-medium">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-700">
                        @foreach ($companies as $company)
                            <tr class="hover:bg-neutral-50 dark:hover:bg-neutral-900/50">
                                <td class="px-4 py-3">
                                    @if ($company->logo)
                                        <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}"
                                             class="size-10 rounded-lg border border-neutral-200 object-cover dark:border-neutral-700">
                                    @else
                                        <div class="flex size-10 items-center justify-center rounded-lg bg-neutral-100 text-neutral-400 dark:bg-neutral-700">
                                            <flux:icon.building-office-2 class="size-5" />
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium">
                                    <flux:link :href="route('companies.show', $company)" wire:navigate>{{ $company->name }}</flux:link>
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $company->email ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    @if ($company->website)
                                        <a href="{{ $company->website }}" target="_blank" rel="noopener"
                                           class="text-blue-600 hover:underline dark:text-blue-400">{{ $company->website }}</a>
                                    @else
                                        <span class="text-neutral-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <flux:badge size="sm" color="zinc">{{ $company->employees_count }}</flux:badge>
                                </td>
                                <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">{{ $company->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:button :href="route('companies.show', $company)" size="sm" variant="ghost" icon="eye" inset="top bottom" />
                                        <flux:button :href="route('companies.edit', $company)" size="sm" variant="ghost" icon="pencil-square" inset="top bottom" />
                                        <form method="POST" action="{{ route('companies.destroy', $company) }}"
                                              onsubmit="return confirm('{{ __('Delete this company? Its employees will also be removed.') }}')">
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

            <div>{{ $companies->onEachSide(1)->links() }}</div>
        @endif
    </div>
</x-layouts::app>
