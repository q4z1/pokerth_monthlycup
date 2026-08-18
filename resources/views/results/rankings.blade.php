@extends('layouts.app')
@section('title', "Rankings $year")

@section('content')
<rankings-component
    :year="{{ $year }}"
    :general="{{ Js::from($general) }}"
    :months="{{ Js::from($months) }}"
    :month-columns="{{ Js::from($monthColumns) }}"
    player-url="{{ config('mcup.player_url') }}"
></rankings-component>
@endsection
