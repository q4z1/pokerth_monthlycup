@extends('layouts.app')
@section('title', 'Settings')

@section('content')
<admin-settings-component
    :year="{{ $year }}"
    :years="{{ Js::from($years) }}"
    :initial-settings="{{ Js::from($settings) }}"
    :months="{{ Js::from($months) }}"
    :final-tables="{{ Js::from($finalTables) }}"
    update-url="{{ route('admin.settings.update') }}"
    season-url="{{ route('admin.seasons.store') }}"
></admin-settings-component>
@endsection
