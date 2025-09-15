<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión — Mentora</title>

  {{-- Tailwind CDN --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // Config rápida (puedes ajustar tu paleta)
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: '#2563eb', // azul
            brandDark: '#1d4ed8',
          }
        }
      }
    }
  </script>

  <style>
    :root{
      --brand: #2563eb;
      --brand-hover: #1d4ed8;
      --bg-soft: #0b122026;
      --ring: rgba(37, 99, 235, .35);
    }
    .brand-btn{ background: var(--brand); color:#fff; }
    .brand-btn:hover{ background: var(--brand-hover); }
    .brand-ring:focus{ outline: none; box-shadow: 0 0 0 4px var(--ring); }
  </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800 antialiased">

  <main class="min-h-screen grid place-items-center px-4">
    <div class="w-full max-w-md">
      <div class="text-center mb-8">
        <a href="/" class="inline-flex items-center gap-2">
          <div class="h-10 w-10 rounded-2xl bg-[var(--bg-soft)] grid place-items-center">
            <span class="text-brand font-bold text-lg">M</span>
          </div>
          <span class="font-semibold text-lg">Mentora</span>
        </a>
        <h1 class="mt-6 text-2xl font-bold">Inicia sesión</h1>
        <p class="mt-2 text-sm text-gray-600">Accede a tu panel con tu correo y contraseña.</p>
      </div>

      @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
          <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}" class="bg-white shadow-sm rounded-2xl p-6 sm:p-8">
        @csrf

        <div class="space-y-1">
          <label for="email" class="block text-sm font-medium">Correo electrónico</label>
          <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email') }}"
            required
            autocomplete="email"
            class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 brand-ring focus:border-brand"
            placeholder="tucorreo@ejemplo.com">
        </div>

        <div class="mt-4 space-y-1">
          <label for="password" class="block text-sm font-medium">Contraseña</label>
          <div class="relative">
            <input
              id="password"
              name="password"
              type="password"
              required
              autocomplete="current-password"
              class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 pr-10 brand-ring focus:border-brand"
              placeholder="••••••••">
            <button type="button" id="togglePass"
              class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-gray-700 focus:outline-none"
              aria-label="Mostrar u ocultar contraseña">
              👁️
            </button>
          </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
          <label class="inline-flex items-center gap-2 text-sm">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand focus:ring-brand">
            <span>Recordarme</span>
          </label>
          {{-- (opcional) link recuperar contraseña
          <a href="#" class="text-sm text-brand hover:text-brandDark">¿Olvidaste tu contraseña?</a>
          --}}
        </div>

        <button type="submit"
          class="mt-6 w-full brand-btn rounded-xl px-4 py-2.5 font-semibold transition-colors">
          Acceder
        </button>

        {{-- (opcional)
        <p class="mt-4 text-center text-sm text-gray-600">
          ¿No tienes cuenta?
          <a href="{{ route('register') }}" class="text-brand hover:text-brandDark font-medium">Crear una</a>
        </p>
        --}}
      </form>

      <p class="mt-8 text-center text-xs text-gray-500">
        © {{ date('Y') }} Mentora. Todos los derechos reservados.
      </p>
    </div>
  </main>

  <script>
    const btn = document.getElementById('togglePass');
    const inp = document.getElementById('password');
    btn?.addEventListener('click', () => {
      inp.type = inp.type === 'password' ? 'text' : 'password';
      btn.textContent = inp.type === 'password' ? '👁️' : '🙈';
    });
  </script>
</body>
</html>