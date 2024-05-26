<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Calendize statistics"
        title="Calendize statistics"
    >
        <x-slot:icon>
            <x-pulse::icons.arrow-trending-up />
        </x-slot:icon>

    </x-pulse::card-header>

    <x-pulse::scroll wire:poll.60s="">
        @if (empty($statistics))
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <tbody>
                @foreach ($statistics as $title => $value)

                    <tr wire:key="{{ $title }}-spacer" class="h-2 first:h-0"></tr>
                    <tr wire:key="{{ $title }}-row">
                        <x-pulse::td class="truncate max-w-[1px]">
                            <div
                                class="py-1 px-12 text-wrap text-base rounded-md text-gray-700 dark:text-gray-300 block whitespace-nowrap overflow-x-auto [scrollbar-color:theme(colors.gray.500)_transparent] [scrollbar-width:thin]">
                                <p>{{ $title }}</p>
                            </div>
                        </x-pulse::td>
                        <x-pulse::td class=" truncate max-w-[1px]">
                            <div
                                class="py-1 text-center text-base rounded-md text-gray-700 dark:text-gray-300 block whitespace-nowrap overflow-x-auto [scrollbar-color:theme(colors.gray.500)_transparent] [scrollbar-width:thin]">
                                <p>{{ $value }}</p>
                            </div>
                        </x-pulse::td>
                    </tr>
                @endforeach
                </tbody>
            </x-pulse::table>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
