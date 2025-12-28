<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- ========== DYNAMIC SEO META TAGS (Server-rendered for crawlers) ========== --}}
    @php
        $meta = isset($page) ? ($page['props']['meta'] ?? []) : [];
        $schema = isset($page) ? ($page['props']['schema'] ?? null) : null;
    @endphp

    <title>{{ $meta['title'] ?? 'SMA Mutiara Insan Nusantara' }}</title>
    <meta name="description"
        content="{{ $meta['description'] ?? 'SMAS Mutiara Insan Nusantara - Sekolah Menengah Atas yang berkomitmen menghasilkan generasi cerdas, berkarakter, dan berprestasi' }}">
    <meta name="keywords"
        content="{{ $meta['keywords'] ?? 'SMA Mutiara Insan Nusantara, mutiara insan nusantara, yayasan mutiara insan nusantara, SMA Yasmin, Yasmin SMA, sma yasmin, SMA Rajeg, sekolah rajeg, yasmin rajeg, sma rajeg tangerang, SMA Tangerang, sma terbaik tangerang, sekolah menengah atas yayasan mutiara insan nusantara, SMAS Mutiara, sekolah menengah atas, pendidikan Tangerang, PPDB, ekstrakurikuler, prestasi siswa, sekolah swasta' }}">
    <meta name="author" content="SMA Mutiara Insan Nusantara">
    <meta name="robots" content="index, follow">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">

    {{-- ========== OPEN GRAPH (Facebook, WhatsApp, LinkedIn) ========== --}}
    <meta property="og:type" content="{{ $meta['og_type'] ?? 'website' }}">
    <meta property="og:title" content="{{ $meta['og_title'] ?? $meta['title'] ?? 'SMA Mutiara Insan Nusantara' }}">
    <meta property="og:description"
        content="{{ $meta['og_description'] ?? $meta['description'] ?? 'Sekolah Menengah Atas yang berkomitmen menghasilkan generasi cerdas, berkarakter, dan berprestasi' }}">
    <meta property="og:image" content="{{ $meta['og_image'] ?? asset('images/og-image.png') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="SMA Mutiara Insan Nusantara">
    <meta property="og:locale" content="id_ID">

    {{-- ========== TWITTER CARDS ========== --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta['title'] ?? 'SMA Mutiara Insan Nusantara' }}">
    <meta name="twitter:description"
        content="{{ $meta['description'] ?? 'Sekolah Menengah Atas yang berkomitmen menghasilkan generasi cerdas, berkarakter, dan berprestasi' }}">
    <meta name="twitter:image" content="{{ $meta['og_image'] ?? asset('images/og-image.png') }}">

    {{-- ========== SCHEMA.ORG STRUCTURED DATA ========== --}}
    @if($schema)
        <script type="application/ld+json">
                {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
            </script>
    @endif

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('img/ico.yasmin.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/favicon-16x16.png') }}">

    {{-- Fonts Preconnect --}}
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap"
            rel="stylesheet">
    </noscript>

    {{-- Preload Images - Only for public pages, not admin panel --}}
    @if(!request()->is('yasmin-panel*'))
        <link rel="preload" href="/images/hero/hero-light.webp" as="image" type="image/webp">
        <link rel="preload" href="/images/hero/hero-dark.webp" as="image" type="image/webp">
        <link rel="preload" href="/img/logo_yasmin.png" as="image" type="image/png">
    @endif

    {{-- Inertia Head - only render if $page exists --}}
    @if(isset($page))
        @inertiaHead
    @endif

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    @if(isset($page))
        @inertia
    @else
        <div id="app"></div>
    @endif
</body>

</html>