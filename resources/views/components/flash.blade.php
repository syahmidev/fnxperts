@if (session('success'))
    <flux:callout variant="success" icon="check-circle" class="mb-6">
        <flux:callout.text>{{ session('success') }}</flux:callout.text>
    </flux:callout>
@endif

@if (session('error'))
    <flux:callout variant="danger" icon="exclamation-triangle" class="mb-6">
        <flux:callout.text>{{ session('error') }}</flux:callout.text>
    </flux:callout>
@endif
