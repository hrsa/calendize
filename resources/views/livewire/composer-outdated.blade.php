<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Outdated Composer Dependencies"
        title="Outdated Composer Dependencies"
    >
        <x-slot:icon>
            <x-pulse::icons.clock />
        </x-slot:icon>

    </x-pulse::card-header>

    <x-pulse::scroll wire:poll.60s="">
        @if (empty($packages))
            <x-pulse::no-results />
        @else
            <x-pulse::table>
                <colgroup>
                    <col width="100%" />
                    <col width="0%" />
                    <col width="0%" />
                </colgroup>
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th>Name</x-pulse::th>
                        <x-pulse::th class="text-center">Current</x-pulse::th>
                        <x-pulse::th class="text-center">Latest</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                @foreach ($packages as $index => $package)
                    <tr wire:key="{{ $index }}-spacer" class="h-2 first:h-0"></tr>
                    <tr wire:key="{{ $index }}-row" >
                        <x-pulse::td class="!p-0 truncate max-w-[1px]">
                            <div class="relative">
                                <div
                                    class="py-4 text-base rounded-md text-gray-700 dark:text-gray-300 block whitespace-nowrap overflow-x-auto [scrollbar-color:theme(colors.gray.500)_transparent] [scrollbar-width:thin]">
                                    <a href="{{ $package['source'] }}"
                                       class="{{ $package['updateType'] === 'minor'
                                            ? 'text-green-500'
                                            : ($package['updateType'] === 'feature'
                                                ? 'dark:!text-yellow-500'
                                                : ($package['updateType'] === 'breaking'
                                                    ? 'text-red-500'
                                                    : 'text-gray-300')) }} px-3 font-bold">{{$package['name']}}</a>
                                </div>
                                <div
                                    class="absolute top-0 right-0 bottom-0 rounded-r-md w-3 pointer-events-none"></div>
                            </div>
                        </x-pulse::td>
                        <x-pulse::td class="text-center text-gray-700 dark:text-gray-300 font-bold">
                            <span class="{{ $package['updateType'] === 'minor'
                                            ? 'text-green-500'
                                            : ($package['updateType'] === 'feature'
                                                ? 'dark:!text-yellow-500'
                                                : ($package['updateType'] === 'breaking'
                                                    ? 'text-red-500'
                                                    : 'text-gray-300')) }} font-bold">{{ $package['version'] }}</span>
                        </x-pulse::td>
                        <x-pulse::td class="text-center text-gray-700 dark:text-gray-300 font-bold">
                            <span class="{{ $package['updateType'] === 'minor'
                                            ? 'text-green-500'
                                            : ($package['updateType'] === 'feature'
                                                ? 'dark:!text-yellow-500'
                                                : ($package['updateType'] === 'breaking'
                                                    ? 'text-red-500'
                                                    : 'text-gray-300')) }} font-bold">{{ $package['latest'] }}</span>
                        </x-pulse::td>
                    </tr>
                @endforeach
                </tbody>
            </x-pulse::table>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
