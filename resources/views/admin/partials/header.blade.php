<header class="page-header admin-page-header">
    <div class="page-header-copy">
        @hasSection('kicker')
            <span class="page-kicker">@yield('kicker')</span>
        @endif
        <h1>@yield('heading', 'Dashboard')</h1>
        <p>@yield('subheading', 'Kelola produk, kategori, pesanan, dan data pendukung Pekarangan Pangan Lestari dari satu panel pengelolaan.')</p>
    </div>

    <div class="page-header-actions">
        @yield('header_actions')
    </div>
</header>
