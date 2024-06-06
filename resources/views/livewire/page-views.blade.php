<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Page views"
        title="Page views"
    >
        <x-slot:icon>
            <x-pulse::icons.sparkles />
        </x-slot:icon>
        <x-slot:actions>
            <x-pulse::select
                wire:model.live="timeline"
                label="Timeline"
                :options="[
                        '24h' => '24h',
                        '7d' => '7d',
                        'Total' => 'Total',
                    ]"
                class="flex-1"
                @change="loading = true"
            />
        </x-slot:actions>
    </x-pulse::card-header>
    <x-pulse::scroll wire:poll.60s="">
        @if (empty($viewsData))
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <tbody>
                @foreach ($viewsData as $page => $stats)

                    <tr wire:key="{{ $page }}-spacer" class="h-2 first:h-0"></tr>
                    <tr wire:key="{{ $page }}-row">
                        <x-pulse::td class="truncate max-w-[1px]">
                            <div
                                class="py-1 px-12 text-wrap text-base rounded-md text-gray-700 dark:text-gray-300 block whitespace-nowrap overflow-x-auto [scrollbar-color:theme(colors.gray.500)_transparent] [scrollbar-width:thin]">
                                <p>{{ $page }}</p>
                            </div>
                        </x-pulse::td>
                        <x-pulse::td class=" truncate max-w-[1px]">
                            <div
                                class="py-1 text-center text-base rounded-md text-gray-700 dark:text-gray-300 block whitespace-nowrap overflow-x-auto [scrollbar-color:theme(colors.gray.500)_transparent] [scrollbar-width:thin]">
                                <p>{{ $stats[$timeline] }}</p>
                            </div>
                        </x-pulse::td>
                    </tr>
                @endforeach
                </tbody>
            </x-pulse::table>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
