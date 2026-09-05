@extends('layouts.app')
@section('title', 'Admin')

@section('content')
<h1 class="page-title">Welcome {{ auth()->user()->displayName() }}</h1>
<p class="page-subtitle">Season {{ $year }} &middot; {{ $cup['month_name'] }} Cup
    @if($cup['date_label']) &middot; {{ $cup['date_label'] }} @endif
</p>

<admin-dashboard-component
    :stats="{{ Js::from($stats) }}"
    :links="{{ Js::from([
        ['label' => 'Upload 1st round table', 'url' => route('admin.upload.firstround'), 'icon' => 'Upload'],
        ['label' => 'Upload final table', 'url' => route('admin.upload.final'), 'icon' => 'Trophy'],
        ['label' => 'Awards', 'url' => route('admin.awards'), 'icon' => 'Medal'],
        ['label' => 'Signups', 'url' => route('admin.signups'), 'icon' => 'UserFilled'],
        ['label' => 'Randomizer', 'url' => route('admin.randomizer'), 'icon' => 'Sort'],
        ['label' => 'Forum posts', 'url' => route('admin.forum-posts'), 'icon' => 'Files'],
        ['label' => 'Settings', 'url' => route('admin.settings'), 'icon' => 'Setting'],
    ]) }}"
></admin-dashboard-component>
@endsection
