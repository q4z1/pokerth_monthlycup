<!doctype html>
<html lang="en" class="{{ request()->cookie('theme') === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) {{ $navSelectedYear }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon-180.png') }}">
    @vite(['resources/js/app.js', 'resources/sass/app.scss'])
</head>
<body>
    <div id="app">
        <nav class="main-navbar">
            <div class="navbar-brand">
                <a href="{{ route('home') }}">PokerTH Monthly Cup</a>
                <button type="button" class="main-navbar-toggler" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <div class="main-navbar-end">
                <el-tooltip content="Switch theme" placement="bottom-end">
                    <el-button circle @click="toggleTheme">
                        <el-icon><moon v-if="theme === 'light'"></moon><sunny v-else></sunny></el-icon>
                    </el-button>
                </el-tooltip>

                <el-dropdown trigger="click" placement="bottom-end">
                    <button type="button" class="navbar-user-trigger" title="Account">
                        <el-icon><avatar></avatar></el-icon>
                        @auth<strong>{{ auth()->user()->displayName() }}</strong>@endauth
                    </button>
                    <template v-slot:dropdown>
                        <el-dropdown-menu>
                            @guest
                                <el-dropdown-item onclick="window.location.href='{{ route('login') }}'">Login</el-dropdown-item>
                            @else
                                <el-dropdown-item onclick="window.location.href='{{ route('admin.dashboard') }}'">Admin</el-dropdown-item>
                                <el-dropdown-item divided onclick="document.getElementById('logout-form').submit()">Logout</el-dropdown-item>
                            @endguest
                        </el-dropdown-menu>
                    </template>
                </el-dropdown>
            </div>

            <div :class="['main-navbar-collapse', { 'is-open': mobileMenuOpen }]">
                <el-menu mode="horizontal" :ellipsis="!mobileMenuOpen" style="width:100%;" class="main-navbar-items">
                    @auth
                    <el-sub-menu index="admin">
                        <template v-slot:title><el-icon><tools></tools></el-icon>&nbsp;<strong>Admin</strong></template>
                        <el-menu-item index="admin-first"><a href="{{ route('admin.upload.firstround') }}">Upload 1st round</a></el-menu-item>
                        <el-menu-item index="admin-final"><a href="{{ route('admin.upload.final') }}">Upload final table</a></el-menu-item>
                        <el-menu-item index="admin-awards"><a href="{{ route('admin.awards') }}">Awards</a></el-menu-item>
                        <el-menu-item index="admin-signups"><a href="{{ route('admin.signups') }}">Signups</a></el-menu-item>
                        <el-menu-item index="admin-random"><a href="{{ route('admin.randomizer') }}">Randomizer</a></el-menu-item>
                        <el-menu-item index="admin-settings"><a href="{{ route('admin.settings') }}">Settings</a></el-menu-item>
                    </el-sub-menu>
                    @endauth

                    <el-sub-menu index="results">
                        <template v-slot:title><el-icon><notebook></notebook></el-icon>&nbsp;Results</template>
                        @for($m = 1; $m <= $navLastMonth; $m++)
                        <el-menu-item index="cup-{{ $m }}"><a href="{{ route('results.cup', ['month' => $m, 'year' => $navSelectedYear]) }}">{{ $navMonths[$m] }} Cup Standings</a></el-menu-item>
                        @endfor
                        <el-menu-item index="series" divided><a href="{{ route('results.series', ['year' => $navSelectedYear]) }}">Series Results</a></el-menu-item>
                        <el-menu-item index="rankings"><a href="{{ route('results.rankings', ['year' => $navSelectedYear]) }}">Series Rankings</a></el-menu-item>
                        <el-menu-item index="hof"><a href="{{ route('results.halloffame', ['year' => $navSelectedYear]) }}">Hall of Fame</a></el-menu-item>
                        <el-menu-item index="points"><a href="{{ route('results.points', ['year' => $navSelectedYear]) }}">Cup Ranking Points</a></el-menu-item>
                    </el-sub-menu>

                    <el-sub-menu index="archive">
                        <template v-slot:title><el-icon><files></files></el-icon>&nbsp;Archive</template>
                        @foreach($navSeasons as $season)
                        <el-menu-item index="archive-{{ $season }}"><a href="{{ route('results.series', ['year' => $season]) }}">Season {{ $season }}</a></el-menu-item>
                        @endforeach
                    </el-sub-menu>

                    <el-menu-item index="registration"><a href="{{ route('registration') }}"><el-icon><edit></edit></el-icon>&nbsp;Registration</a></el-menu-item>
                    <el-menu-item index="signups"><a href="{{ route('signups') }}"><el-icon><user-filled></user-filled></el-icon>&nbsp;Signups</a></el-menu-item>
                    <el-menu-item index="table-settings"><a href="{{ route('table-settings') }}"><el-icon><setting></setting></el-icon>&nbsp;Table Settings</a></el-menu-item>
                </el-menu>
            </div>
        </nav>

        @auth
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
        @endauth

        <main>
            <div class="page">
                @if(session('status'))
                <el-alert type="success" :closable="true" show-icon title="{{ session('status') }}" style="margin-bottom:1rem"></el-alert>
                @endif
                @yield('content')
            </div>
        </main>

        <footer class="page-footer">
            @if($navFooter)
            <div>{!! $navFooter !!}</div>
            @endif
            <div>
                <a href="https://pokerth.net" target="_blank" rel="noopener">
                    <img src="{{ asset('images/pth_logo.png') }}" alt="PokerTH">
                </a>
            </div>
            <div style="margin-top:0.75rem">
                <a href="https://pokerth.net/app.php/imprint" target="_blank" rel="noopener">Imprint</a>
            </div>
        </footer>
    </div>
</body>
</html>
