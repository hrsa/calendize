<x-mail.base subject="Sorry, i couldn't do it!" :$ics>
    <p style="text-align: center;">I can't generate an ICS event out ot that.</p>

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
        {{$ics->error}}
    </p>

    <p style="padding-top: 0.5rem;
border-radius: 0.5rem;
font-size: 0.875rem;
line-height: 1.25rem;
text-align: center;
color: #9CA3AF;
    ">
        @if($ics->user->credits > 0)
            You have <span style="font-size: 1rem;
line-height: 1.5rem;
font-weight: 600;
color: #E5E7EB;
">{{$ics->user->credits}}</span> {{ \Illuminate\Support\Str::plural('credit', $ics->user->credits) }} left, by the
            way.<br>
        @else
            You have no credits left!<br>
        @endif

        @if($ics->user->credits < 2)
            Visit you dashboard for a top-up - or a new subscription!
        @endif
    </p>
    @if($ics->user->credits < 2)
        <a href="{{route('dashboard')}}"
           style="
margin-top: 2rem;
border-radius: 0.5rem;
padding: 0.5rem 1rem;
text-decoration: none;
text-transform: uppercase;
font-size: 1rem;
font-weight: 600;
background-color: darkgreen;
line-height: 1.25rem;
text-align: center;
color: #dde3e8;
    ">
            Get more credits
        </a>
    @endif


</x-mail.base>

