<x-pulse>
    @vite(['resources/css/app.css'])

    <livewire:pulse.servers cols="full" />
    <livewire:statistics cols="4" rows="2" />
    <livewire:pulse.usage cols="4" rows="2" />

    <livewire:composer-outdated cols="4" />


    <livewire:pulse.queues cols="4" />

    <livewire:pulse.cache cols="4" />

    <livewire:pulse.slow-queries cols="8" />

    <livewire:pulse.exceptions cols="6" />

    <livewire:pulse.slow-requests cols="6" />

    <livewire:pulse.slow-jobs cols="6" />

    <livewire:pulse.slow-outgoing-requests cols="6" />
</x-pulse>
