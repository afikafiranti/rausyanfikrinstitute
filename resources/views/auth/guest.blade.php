<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Rausyan Fikr' }}</title>

  {{-- muat asset hanya di non-testing --}}
  @if (!app()->environment('testing'))
    @vite(['resources/css/app.css','resources/js/app.js'])
  @endif

  <style>
    /* fallback styling ringan agar halaman tetap rapi saat testing */
    body{font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f8fafc;margin:0}
    .rf-container{max-width:420px;margin:4rem auto;padding:2rem;background:#fff;border-radius:1rem;box-shadow:0 10px 20px rgba(0,0,0,.06)}
    .rf-section h1{margin:0 0 .75rem}
    .rf-btn{display:inline-block;background:#4f46e5;color:#fff;padding:.625rem 1rem;border-radius:.5rem;border:0;cursor:pointer}
    input[type=email],input[type=password]{border:1px solid #cbd5e1;padding:.5rem .75rem}
  </style>
</head>
<body>
  <main class="rf-container">
    @yield('content')
  </main>
</body>
</html>
