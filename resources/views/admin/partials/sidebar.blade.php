<aside class="admin-sidebar admin-sidebar-feature">
    <div class="sidebar-top">
        <a href="{{ route('admin.dashboard') }}" class="brand-block">
            <div class="brand-main">
                <img src="{{ asset('images/logo-putih.png') }}" alt="P2L Hebat" class="brand-logo">
                <div>
                    <strong>P2L Hebat</strong>
                    <span>Pekarangan Pangan Lestari</span>
                </div>
            </div>
        </a>

        <nav class="admin-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.kategoris.index') }}" class="{{ request()->routeIs('admin.kategoris.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i>
                <span>Kategori</span>
            </a>
            <a href="{{ route('admin.produks.index') }}" class="{{ request()->routeIs('admin.produks.*') ? 'active' : '' }}">
                <i class="fa-solid fa-basket-shopping"></i>
                <span>Produk</span>
            </a>
            <a href="{{ route('admin.pesanan.index') }}" class="{{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Pesanan</span>
            </a>
            <a href="{{ route('admin.payment-methods.index') }}" class="{{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card"></i>
                <span>Pembayaran</span>
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-column"></i>
                <span>Laporan</span>
            </a>
            @if(auth()->user()->role === 'super_admin')
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>Pengguna</span>
                </a>
                <a href="{{ route('admin.activity-logs.index') }}" class="{{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Log Aktivitas</span>
                </a>
            @endif
            <a href="{{ route('profil.edit') }}">
                <i class="fa-solid fa-user-gear"></i>
                <span>Edit Profil</span>
            </a>
            <a href="{{ route('beranda') }}">
                <i class="fa-solid fa-globe"></i>
                <span>Lihat Website</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <div class="user-summary">
            <span class="user-summary-kicker">Akun aktif</span>
            <strong>{{ auth()->user()->nama }}</strong>
            <span>{{ auth()->user()->role }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}" id="admin-logout-form">
            @csrf
            <button type="button" class="btn btn-logout" data-logout-trigger data-logout-target="admin-logout-form">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
