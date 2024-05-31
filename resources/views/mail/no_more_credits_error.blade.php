<x-mail.base subject="I got your email, but I can't process it yet..." logo="sad">
    <p style="text-align: center;">Your balance is zero! 🙁</p>

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
        Please, update your subscription plan or top up some credits!<br>
        I'll calendize your email as soon as your balance is updated.
    </p>
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
            Open dashboard
        </a>


</x-mail.base>

