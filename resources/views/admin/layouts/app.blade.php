<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    @include('partials.login-success-toast')
    @include('partials.action-success-toast')
    <div class="admin-shell">
        @include('admin.partials.sidebar')

        <main class="admin-main">
            <div class="admin-main-orb admin-main-orb-one"></div>
            <div class="admin-main-orb admin-main-orb-two"></div>
            @unless(View::hasSection('hide_admin_header'))
                @include('admin.partials.header')
            @endunless
            @include('admin.partials.flash')

            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>
    @include('partials.logout-modal')
    @include('partials.action-confirm-modal')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('admin-motion-ready');

            const revealTargets = [
                ...document.querySelectorAll('.admin-page-header, .admin-hero, .admin-card, .sidebar-top, .sidebar-footer'),
                ...document.querySelectorAll('.admin-nav a, .admin-table tbody tr'),
            ];

            revealTargets.forEach((element, index) => {
                element.classList.add('admin-reveal');
                element.style.setProperty('--reveal-delay', `${Math.min(index * 0.06, 0.42)}s`);
            });

            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -8% 0px',
            });

            revealTargets.forEach((element) => revealObserver.observe(element));

            const interactiveCards = document.querySelectorAll('.admin-card, .admin-hero, .user-summary');

            interactiveCards.forEach((card) => {
                card.addEventListener('pointermove', (event) => {
                    if (window.innerWidth < 961) {
                        return;
                    }

                    const rect = card.getBoundingClientRect();
                    const x = ((event.clientX - rect.left) / rect.width) * 100;
                    const y = ((event.clientY - rect.top) / rect.height) * 100;

                    card.style.setProperty('--pointer-x', `${x}%`);
                    card.style.setProperty('--pointer-y', `${y}%`);
                });

                card.addEventListener('pointerleave', () => {
                    card.style.removeProperty('--pointer-x');
                    card.style.removeProperty('--pointer-y');
                });
            });
        });
    </script>
</body>
</html>
