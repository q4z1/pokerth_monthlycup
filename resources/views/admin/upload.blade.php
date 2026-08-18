@extends('layouts.app')
@section('title', $title)

@section('content')
<upload-component
    mode="{{ $mode }}"
    title="{{ $title }}"
    :year="{{ $year }}"
    :month="{{ $month }}"
    :months="{{ Js::from($months) }}"
    :tables="{{ Js::from($tables) }}"
    preview-url="{{ route('admin.upload.preview') }}"
    store-url="{{ route('admin.upload.store') }}"
></upload-component>
@endsection
