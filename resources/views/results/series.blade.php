@extends('layouts.app')
@section('title', 'Series Results')
@section('description', "Winners of every PokerTH Monthly Cup of the season, with the awards handed out.")

@section('content')
<series-component
    :year="{{ $year }}"
    :cups="{{ Js::from($cups) }}"
    player-url="{{ config('mcup.player_url') }}"
></series-component>
@endsection
