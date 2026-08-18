@extends('layouts.app')
@section('title', 'PokerTH Monthly Cup')

@section('content')
<div class="site-hero">
    <img src="{{ asset('images/mcup_2026_theme.jpg') }}" alt="PokerTH Monthly Cup Series {{ $year }}">
</div>

<home-component
    :year="{{ $year }}"
    :next-cup="{{ Js::from($nextCup) }}"
    :signup-count="{{ $signupCount }}"
    :latest-cup="{{ Js::from($latestCup) }}"
    registration-url="{{ route('registration') }}"
    signups-url="{{ route('signups') }}"
    results-url="{{ route('results.series', ['year' => $year]) }}"
></home-component>
@endsection
