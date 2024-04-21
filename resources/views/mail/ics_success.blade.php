<x-mail.base subject="Your event was calendized!" :ics="$ics">
    <p class="text-center">Great news! I've identified the event and calendized it 😊</p>

    <a href="{{route('event.download', ['id' => $ics->id, 'secret' => $ics->secret])}}"><p class="text-center my-6 p-6 border-2 border-gray-700 rounded-lg text-lg text-gray-400">
            {{$ics->getSummary()}}
        </p></a>

    <p class="text-center pt-2 rounded-lg text-sm text-gray-400">
        You have <span class="text-gray-200 font-semibold text-base">{{$ics->user->credits}}</span> credits left, by the
        way.</p>
</x-mail.base>

