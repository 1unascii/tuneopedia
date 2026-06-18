@props(['title' => null])
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - Tuneopedia' : 'Tuneopedia' }}</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fondamento:ital@0;1&family=Noto+Music&family=Rye&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <script src="https://kit.fontawesome.com/5c647e36cb.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            var saved = localStorage.getItem('tuneopedia-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', saved === 'light' ? 'light' : 'dark');
        })();
    </script>
</head>
