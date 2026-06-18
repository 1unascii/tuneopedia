{{--
    Navigation Bar
    ==============
    Active link highlighting:
      - routeIs() is preferred — it matches against named routes, avoiding wildcard conflicts.
        e.g. routeIs('tunes.index', 'tunes.show') highlights "Tunes" but not "Add Tune".
      - request()->is() is used as a fallback for routes that don't have names yet.
        Once those routes are given names in web.php, switch them to routeIs().
--}}
<nav class="nav-overlay flex items-center gap-10 px-4 py-3 w-full -mt-[60px] mb-10 z-10 relative font-[Fondamento] bg-base-300/80 backdrop-blur-[10px]">
    <a href="/" class="nav-link {{ request()->routeIs('home') ? 'nav-link-active' : '' }}">Home</a>
    <a href="/tunes" class="nav-link {{ request()->routeIs('tunes.index', 'tunes.show') ? 'nav-link-active' : '' }}">Tunes</a>

    <a href="{{ route('collections.index') }}" class="nav-link {{ request()->routeIs('collections.*') ? 'nav-link-active' : '' }}">Collections</a>

    {{-- TODO: Most discussion routes are unnamed — using request()->is() until they are named.
         Once named, change to routeIs('discussion-threads.index', 'discussion-threads.show') --}}
    <a href="/discussion-threads" class="nav-link {{ request()->is('discussion-threads*') ? 'nav-link-active' : '' }}">Discussions</a>

    @auth
        <a href="{{ route('favorites.index') }}" class="nav-link {{ request()->routeIs('favorites.*') ? 'nav-link-active' : '' }}">Favorites</a>
    @endauth
    <div class="ml-auto flex items-center gap-6">
        @auth
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">{{ Auth::user()->name }}</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link">Log Out</button>
            </form>
        @else
            {{-- TODO: Auth routes use request()->is() — could use routeIs('login')/routeIs('register')
                 but these Breeze routes are already named so either approach works --}}
            <a href="{{ route('login') }}" class="nav-link {{ request()->routeIs('login') ? 'nav-link-active' : '' }}">Log In</a>
            <a href="{{ route('register') }}" class="nav-link {{ request()->routeIs('register') ? 'nav-link-active' : '' }}">Register</a>
        @endauth
        <button id="theme-toggle" class="nav-link" title="Toggle light/dark mode">
            <i class="fa-solid fa-sun" id="theme-icon"></i>
        </button>
    </div>
</nav>
