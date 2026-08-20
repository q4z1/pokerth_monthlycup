@extends('layouts.app')
@section('title', 'Login')
@section('description', 'Administration login for the PokerTH Monthly Cup.')

@section('content')
<login-component
    action="{{ route('login') }}"
    :errors="{{ Js::from($errors->getMessages()) }}"
></login-component>
@endsection
