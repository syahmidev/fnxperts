@php($employee = $employee ?? null)

@if ($companies->isEmpty())
    <flux:callout variant="warning" icon="exclamation-triangle" class="max-w-2xl">
        <flux:callout.heading>{{ __('No companies available') }}</flux:callout.heading>
        <flux:callout.text>
            {{ __('You need at least one company before adding an employee.') }}
        </flux:callout.text>
        <x-slot name="actions">
            <flux:button :href="route('companies.create')" size="sm">{{ __('Create a company') }}</flux:button>
        </x-slot>
    </flux:callout>
@else
    <form
        method="POST"
        action="{{ $employee ? route('employees.update', $employee) : route('employees.store') }}"
        class="flex max-w-2xl flex-col gap-6"
    >
        @csrf
        @if ($employee)
            @method('PUT')
        @endif

        <div class="grid gap-6 sm:grid-cols-2">
            <flux:input
                name="first_name"
                :label="__('First Name')"
                type="text"
                required
                badge="{{ __('Required') }}"
                :value="old('first_name', $employee?->first_name)"
            />
            <flux:input
                name="last_name"
                :label="__('Last Name')"
                type="text"
                required
                badge="{{ __('Required') }}"
                :value="old('last_name', $employee?->last_name)"
            />
        </div>

        <flux:select
            name="company_id"
            :label="__('Company')"
            required
            badge="{{ __('Required') }}"
            :placeholder="__('Choose a company...')"
        >
            @foreach ($companies as $company)
                <flux:select.option value="{{ $company->id }}" :selected="(int) old('company_id', $employee?->company_id) === $company->id">
                    {{ $company->name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:input
            name="email"
            :label="__('Email')"
            type="email"
            :value="old('email', $employee?->email)"
        />

        <flux:input
            name="phone"
            :label="__('Phone')"
            type="text"
            :value="old('phone', $employee?->phone)"
        />

        <div class="flex items-center gap-3">
            <flux:button type="submit" variant="primary">
                {{ $employee ? __('Update Employee') : __('Create Employee') }}
            </flux:button>
            <flux:button :href="$employee ? route('employees.show', $employee) : route('employees.index')" variant="ghost">
                {{ __('Cancel') }}
            </flux:button>
        </div>
    </form>
@endif
