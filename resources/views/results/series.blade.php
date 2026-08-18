@extends('layouts.app')
@section('title', "Series $year Results")

@section('content')
<series-component
    :year="{{ $year }}"
    :cups="{{ Js::from($cups) }}"
    player-url="{{ config('mcup.player_url') }}"
></series-component>
@endsection
