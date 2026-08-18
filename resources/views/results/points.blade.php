@extends('layouts.app')
@section('title', "Cup Ranking Points $year")

@section('content')
<points-component :year="{{ $year }}" :rows="{{ Js::from($rows) }}"></points-component>
@endsection
