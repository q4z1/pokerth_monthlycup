@extends('layouts.app')
@section('title', 'Signups')

@section('content')
<admin-signups-component
    :year="{{ $year }}"
    :month="{{ $month }}"
    month-name="{{ $monthName }}"
    date-label="{{ $dateLabel }}"
    :months="{{ Js::from($months) }}"
    :initial-signups="{{ Js::from($signups) }}"
    base-url="{{ url('/admin/signups') }}"
    player-url="{{ config('mcup.player_url') }}"
></admin-signups-component>
@endsection
