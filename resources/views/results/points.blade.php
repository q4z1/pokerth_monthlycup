@extends('layouts.app')
@section('title', 'Cup Ranking Points')
@section('description', "How many cup ranking points each finishing place is worth at the PokerTH Monthly Cup.")

@section('content')
<points-component :year="{{ $year }}" :rows="{{ Js::from($rows) }}"></points-component>
@endsection
