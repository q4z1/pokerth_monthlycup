@extends('layouts.app')
@section('title', 'Randomizer')

@section('content')
<randomizer-component
    :year="{{ $year }}"
    :month="{{ $month }}"
    month-name="{{ $monthName }}"
    :players="{{ Js::from($players) }}"
    :substitutes="{{ Js::from($substitutes) }}"
    generated-at="{{ $generatedAt }}"
></randomizer-component>
@endsection
