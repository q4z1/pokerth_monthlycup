@extends('layouts.app')
@section('title', "$monthName Cup")
@section('description', "Full table results of a single PokerTH Monthly Cup: first round tables and the gold, silver and bronze finals.")

@section('content')
<cup-component
    :year="{{ $year }}"
    :month="{{ $month }}"
    month-name="{{ $monthName }}"
    :tables="{{ Js::from($tables) }}"
    player-url="{{ config('mcup.player_url') }}"
></cup-component>
@endsection
