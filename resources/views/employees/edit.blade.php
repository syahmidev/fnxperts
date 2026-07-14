<x-layouts::app :title="__('Edit Employee')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex flex-col gap-1">
            <flux:breadcrumbs>
                <flux:breadcrumbs.item :href="route('employees.index')" wire:navigate>{{ __('Employees') }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item :href="route('employees.show', $employee)" wire:navigate>{{ $employee->full_name }}</flux:breadcrumbs.item>
                <flux:breadcrumbs.item>{{ __('Edit') }}</flux:breadcrumbs.item>
            </flux:breadcrumbs>
            <flux:heading size="xl">{{ __('Edit Employee') }}</flux:heading>
        </div>

        @include('employees._form')
    </div>
</x-layouts::app>
