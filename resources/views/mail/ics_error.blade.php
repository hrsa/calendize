<x-mail.base subject="Sorry, i couldn't do it!" :ics="$ics">
    <p class="text-center">I can't generate an ICS event out ot that.</p>

    <p class="text-center my-6 p-6 border-2 border-gray-700 rounded-lg text-lg text-gray-400">{{$ics->error}}</p>

    <p class="text-center pt-2 rounded-lg text-sm text-gray-400">
        You have <span class="text-gray-200 font-semibold text-base">{{$ics->user->credits}}</span> credits left, by the
        way.</p>
</x-mail.base>

