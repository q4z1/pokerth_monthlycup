@extends('layouts.app')
@section('title', "$monthName Cup $year")

@section('content')
<cup-component
    :year="{{ $year }}"
    :month="{{ $month }}"
    month-name="{{ $monthName }}"
    :tables="{{ Js::from($tables) }}"
    player-url="{{ config('mcup.player_url') }}"
></cup-component>
@endsection
