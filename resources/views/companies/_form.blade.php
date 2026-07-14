@php($company = $company ?? null)

<form
    method="POST"
    action="{{ $company ? route('companies.update', $company) : route('companies.store') }}"
    enctype="multipart/form-data"
    class="flex max-w-2xl flex-col gap-6"
>
    @csrf
    @if ($company)
        @method('PUT')
    @endif

    <flux:input
        name="name"
        :label="__('Name')"
        type="text"
        required
        :value="old('name', $company?->name)"
        badge="{{ __('Required') }}"
    />

    <flux:input
        name="email"
        :label="__('Email')"
        type="email"
        :value="old('email', $company?->email)"
    />

    <flux:input
        name="website"
        :label="__('Website')"
        type="url"
        placeholder="https://example.com"
        :value="old('website', $company?->website)"
    />

    <div class="flex flex-col gap-2">
        <flux:label>{{ __('Logo') }}</flux:label>

        @if ($company?->logo)
            <div class="flex items-center gap-3">
                <img src="{{ $company->logoUrl() }}" alt="{{ $company->name }}"
                     class="size-16 rounded-lg border border-neutral-200 object-cover dark:border-neutral-700">
                <flux:text class="text-sm">{{ __('Current logo') }}</flux:text>
            </div>
        @endif

        <img id="logo-preview" src="" alt="{{ __('Selected logo preview') }}"
             class="hidden size-16 rounded-lg border border-neutral-200 object-cover dark:border-neutral-700">

        <input
            type="file"
            name="logo"
            accept="image/png,image/jpeg,image/webp"
            onchange="const f=this.files[0];const p=document.getElementById('logo-preview');if(f){p.src=URL.createObjectURL(f);p.classList.remove('hidden');}else{p.classList.add('hidden');}"
            class="block w-full text-sm text-neutral-600 file:mr-4 file:rounded-lg file:border-0 file:bg-neutral-100 file:px-4 file:py-2 file:text-sm file:font-medium hover:file:bg-neutral-200 dark:text-neutral-300 dark:file:bg-neutral-700 dark:hover:file:bg-neutral-600"
        >
        <flux:text class="text-xs text-neutral-500">
            {{ __('PNG, JPG or WEBP, min. 100 × 100 px, max 2 MB.') }}
        </flux:text>
        @error('logo')
            <flux:text class="text-sm text-red-600 dark:text-red-400">{{ $message }}</flux:text>
        @enderror
    </div>

    <div class="flex items-center gap-3">
        <flux:button type="submit" variant="primary">
            {{ $company ? __('Update Company') : __('Create Company') }}
        </flux:button>
        <flux:button :href="$company ? route('companies.show', $company) : route('companies.index')" variant="ghost">
            {{ __('Cancel') }}
        </flux:button>
    </div>
</form>
