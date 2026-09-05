@extends('layouts.app')
@section('title', 'PokerTH Monthly Cup')
@section('description', 'The PokerTH Monthly Cup series: cup dates, registration, results and the season ranking.')

@section('content')
<div class="site-hero">
    <img src="{{ asset(config('mcup.theme_image')) }}" alt="PokerTH Monthly Cup Series {{ $year }}">
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
