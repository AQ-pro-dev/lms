<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="{{ asset('assets/js/jquery_min.js') }}"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="{{ asset('assets/Font_Awesome/css/all.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="icon" href="{{ asset('assets/images/fav-icon.webp') }}" type="image/webp">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.tiny.cloud/1/5iv0e4vx3nzuwf3ya5c4epexxpn19n8bs4mnku7wvh2598qz/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @livewireStyles
    <title>@stack('title', 'Page Title')</title>
</head>

<body>
    <main>
        {{ $slot }}
    </main>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireScripts
    <x-livewire-alert::scripts />
    @stack('js')
</body>

</html>
