<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard — Mentora</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
  <div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Dashboard</h1>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="rounded-lg bg-gray-900 text-white px-4 py-2">Cerrar sesión</button>
      </form>
    </div>

    <div class="mt-6 rounded-2xl bg-white p-6 shadow-sm">
      <p class="text-gray-700">
        Hola, <strong>{{ auth()->user()->name ?? auth()->user()->email }}</strong>.
      </p>
      <p class="mt-2 text-sm text-gray-500">Aquí irá tu panel.</p>
    </div>
  </div>
</body>
</html>