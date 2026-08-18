@extends('layouts.app')
@section('title', 'Registration')

@section('content')
<registration-component
    :cup="{{ Js::from($cup) }}"
    :year="{{ $year }}"
    action="{{ route('registration.store') }}"
    signups-url="{{ route('signups') }}"
></registration-component>
@endsection
