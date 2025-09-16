<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Rausyan Fikr — Auth' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-slate-800">
  <header class="py-4">
    <div class="max-w-md mx-auto px-4 flex items-center gap-2">
      <span class="inline-flex h-8 w-8 rounded-xl bg-brand shadow-card"></span>
      <span class="font-semibold">Rausyan Fikr</span>
    </div>
  </header>

  <main class="max-w-md mx-auto p-4">
    @yield('content')
  </main>
</body>
</html>
