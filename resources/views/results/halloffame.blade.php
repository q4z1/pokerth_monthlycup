@extends('layouts.app')
@section('title', "Hall of Fame $year")

@section('content')
<hall-of-fame-component
    :year="{{ $year }}"
    :players="{{ Js::from($players) }}"
    player-url="{{ config('mcup.player_url') }}"
></hall-of-fame-component>
@endsection
