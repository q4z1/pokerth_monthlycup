@extends('layouts.app')
@section('title', 'Forum posts')

@section('content')
<forum-posts-component
    :year="{{ $year }}"
    :month="{{ $month }}"
    month-name="{{ $monthName }}"
    :months="{{ Js::from($months) }}"
    :config="{{ Js::from($config) }}"
    :signup-count="{{ $signupCount }}"
    :announcement="{{ Js::from($announcement) }}"
    :seeding="{{ Js::from($seeding) }}"
    :seeding-tables="{{ Js::from($seedingTables) }}"
    :seeding-substitutes="{{ Js::from($seedingSubstitutes) }}"
    :final-seeding="{{ Js::from($finalSeeding) }}"
    :results="{{ Js::from($results) }}"
    save-url="{{ route('admin.forum-posts.config') }}"
    shuffle-url="{{ route('admin.forum-posts.shuffle') }}"
></forum-posts-component>
@endsection
