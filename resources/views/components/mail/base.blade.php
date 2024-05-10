<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xmlns="http://www.w3.org/1999/xhtml"
      xmlns:v="urn:schemas-microsoft-com:vml"
      xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    @vite(['resources/css/app.css'])
    {{--
    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
    --}}

    {{--@if($subject === $attributes->get('subject'))--}}
    <title>{{ $subject }}</title>
    {{--@endif--}}
</head>
<body>
<div style="-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
color: #E5E7EB; background-color: #111827; text-align: center; margin: auto">
    @if($ics?->ics)
    <img src={{asset('calendar.png')}} alt="Calendize logo" style="margin: 1.5rem auto auto;height: 6rem"/>
    @endif
    @if($ics?->error)
    <img src={{asset('calendar-sad.png')}} alt="Calendize logo" style="margin: 1.5rem auto auto;height: 6rem;"/>
    @endif

    <div style="padding: 3rem 2rem;
height: 100%; ">
        <div style="
padding: 1rem 1.5rem;
margin: auto auto 1rem;
border-radius: 0.5rem;
max-width: 36rem;
font-size: 1.125rem;
line-height: 1.75rem;
font-weight: 600;
text-align: center;
color: #E5E7EB;
background-color: #1F2937;
box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); ">
            {{ $subject }}
        </div>
        <div style="margin: auto; padding: 1.5rem;
    border-radius: 0.5rem;
    max-width: 36rem;
    background-color: #1F2937;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    ">
            {{ $slot }}
        </div>
    </div>
</div>
</body>
</html>
