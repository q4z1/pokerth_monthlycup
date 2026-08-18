@extends('layouts.app')
@section('title', 'Login')

@section('content')
<login-component
    action="{{ route('login') }}"
    :errors="{{ Js::from($errors->getMessages()) }}"
></login-component>
@endsection
