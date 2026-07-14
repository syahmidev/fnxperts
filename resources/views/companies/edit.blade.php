<x-layouts::app :title="__('Edit Company')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-1">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('companies.index')" wire:navigate>{{ __('Companies') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item :href="route('companies.show', $company)" wire:navigate>{{ $company->name }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Edit') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <flux:heading size="xl">{{ __('Edit Company') }}</flux:heading>
        </div>

        @include('companies._form')
    </div>
</x-layouts::app>
