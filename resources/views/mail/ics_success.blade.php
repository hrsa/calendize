<x-mail.base subject="Your event was calendized!" :$ics>
    <p style="text-align: center;">Great news! I've identified the event and calendized it 😊</p>

    <a href="{{ route('event.download', ['id' => $ics->id, 'secret' => $ics->secret]) }}">
        <p style="padding: 1.5rem;
margin-top: 1.5rem;
margin-bottom: 1.5rem;
border-radius: 0.5rem;
border-width: 2px;
border-color: #374151;
font-size: 1.125rem;
line-height: 1.75rem;
text-align: center;
color: #9CA3AF;
">
            {{$ics->getSummary()}}
        </p></a>

    <p style="padding-top: 0.5rem;
border-radius: 0.5rem;
font-size: 0.875rem;
line-height: 1.25rem;
text-align: center;
color: #9CA3AF;
">
        You have <span style="font-size: 1rem;
line-height: 1.5rem;
font-weight: 600;
color: #E5E7EB;
">
            {{ $ics->user->credits }}</span> credits left, by the
        way.</p>
</x-mail.base>

