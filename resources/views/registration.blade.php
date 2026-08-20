@extends('layouts.app')
@section('title', 'Registration')
@section('description', 'Register for the next PokerTH Monthly Cup. Registration closes one hour before the cup starts.')

@section('content')
<registration-component
    :cup="{{ Js::from($cup) }}"
    :year="{{ $year }}"
    action="{{ route('registration.store') }}"
    signups-url="{{ route('signups') }}"
></registration-component>
@endsection
