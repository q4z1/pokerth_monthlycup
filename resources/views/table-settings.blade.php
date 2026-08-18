@extends('layouts.app')
@section('title', 'Table Settings')

@section('content')
<h1 class="page-title">Table Settings</h1>
<p class="page-subtitle">The game settings used for every Monthly Cup table</p>

<div class="panel">
    <h2 class="section-title">First round tables</h2>
    <ul>
        <li>game type: <em>only invited players</em></li>
        <li>start cash: 10.000$</li>
        <li>first small blind: 50$</li>
        <li>double blinds: every 16 hands</li>
        <li>timeout for action: 10 sec</li>
        <li>delay between hands: 7 sec</li>
    </ul>
</div>

<div class="panel">
    <h2 class="section-title">Final tables (Gold / Silver / Bronze)</h2>
    <ul>
        <li>game type: <em>only invited players</em></li>
        <li>start cash: 10.000$</li>
        <li>first small blind: 50$</li>
        <li>double blinds: every 22 hands</li>
        <li>timeout for action: 12 sec</li>
        <li>delay between hands: 7 sec</li>
    </ul>
</div>
@endsection
