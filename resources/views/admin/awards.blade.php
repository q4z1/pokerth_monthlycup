@extends('layouts.app')
@section('title', 'Awards')

@section('content')
<admin-awards-component
    :year="{{ $year }}"
    :month="{{ $month }}"
    :initial-awards="{{ Js::from($awards) }}"
    :players="{{ Js::from($players) }}"
    :types="{{ Js::from($types) }}"
    :months="{{ Js::from($months) }}"
    store-url="{{ route('admin.awards.store') }}"
    base-url="{{ url('/admin/awards') }}"
></admin-awards-component>
@endsection
