@extends('layouts.app')
@section('title', 'Rankings')
@section('description', "Overall and per-cup ranking points of the PokerTH Monthly Cup season.")

@section('content')
<rankings-component
    :year="{{ $year }}"
    :general="{{ Js::from($general) }}"
    :months="{{ Js::from($months) }}"
    :month-columns="{{ Js::from($monthColumns) }}"
    player-url="{{ config('mcup.player_url') }}"
></rankings-component>
@endsection
