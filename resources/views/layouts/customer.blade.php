<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'TopUp Kilat') - Top Up Game Cepat & Aman</title>
    <meta name="description" content="@yield('meta_description', 'Top up Diamond, Voucher, dan item game favoritmu. Proses otomatis 1-3 menit.')">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

    @stack('styles')
</head>
<body>

    @include('customer.partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('customer.partials.footer')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script>
        window.APP_CONFIG = {
            apiBase: '{{ url('/api/v1') }}',
        };
    </script>
    @stack('scripts')
</body>
</html>
