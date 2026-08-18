@extends('layouts.app')
@section('title', 'Signups')

@section('content')
<signup-list-component
    :cup="{{ Js::from($cup) }}"
    :players="{{ Js::from($players) }}"
    :substitutes="{{ Js::from($substitutes) }}"
    :pending="{{ $pending }}"
    registration-url="{{ route('registration') }}"
    player-url="{{ config('mcup.player_url') }}"
></signup-list-component>
@endsection
