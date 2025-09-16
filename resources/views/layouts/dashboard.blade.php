<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Dashboard • Mentora')</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              50:  'rgb(var(--brand-50) / <alpha-value>)',
              100: 'rgb(var(--brand-100) / <alpha-value>)',
              200: 'rgb(var(--brand-200) / <alpha-value>)',
              300: 'rgb(var(--brand-300) / <alpha-value>)',
              400: 'rgb(var(--brand-400) / <alpha-value>)',
              500: 'rgb(var(--brand-500) / <alpha-value>)',
              600: 'rgb(var(--brand-600) / <alpha-value>)',
              700: 'rgb(var(--brand-700) / <alpha-value>)',
              800: 'rgb(var(--brand-800) / <alpha-value>)',
              900: 'rgb(var(--brand-900) / <alpha-value>)'
            },
            surface: 'rgb(var(--surface) / <alpha-value>)',
            card: 'rgb(var(--card) / <alpha-value>)',
            content: 'rgb(var(--content) / <alpha-value>)',
            muted: 'rgb(var(--muted) / <alpha-value>)',
            ring: 'rgb(var(--ring) / <alpha-value>)',
            accent: 'rgb(var(--accent) / <alpha-value>)'
          },
          boxShadow: {
            soft: '0 10px 30px -12px rgb(0 0 0 / 0.15)'
          },
          borderRadius: {
            xl2: '1rem'
          }
        }
      }
    }
  </script>

  <style>
    :root {
      --brand-50: 239 246 255;
      --brand-100: 219 234 254;
      --brand-200: 191 219 254;
      --brand-300: 147 197 253;
      --brand-400: 96 165 250;
      --brand-500: 59 130 246;
      --brand-600: 37 99 235;
      --brand-700: 29 78 216;
      --brand-800: 30 64 175;
      --brand-900: 30 58 138;
      --surface: 249 250 251;
      --card: 255 255 255;
      --content: 17 24 39;
      --muted: 107 114 128;
      --ring: 59 130 246;
      --accent: 16 185 129;
    }
    .dark {
      --surface: 17 24 39;
      --card: 30 41 59;
      --content: 226 232 240;
      --muted: 148 163 184;
      --ring: 59 130 246;
      --accent: 20 184 166;
    }
    .card {
      background-color: rgb(var(--card));
      box-shadow: 0 1px 2px rgb(0 0 0 / 0.06), 0 10px 20px -15px rgb(0 0 0 / 0.15);
    }
  </style>
  @stack('head')
</head>
<body class="h-full bg-surface text-content">
  <div class="min-h-screen flex" data-component="RootLayout">
    <x-dashboard.sidebar />
    <div id="overlay" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-30 hidden md:hidden" aria-hidden="true"></div>
    <div class="flex-1 flex flex-col min-w-0" data-component="MainArea">
      <x-dashboard.topbar />
      <main data-component="PageContent" class="p-4 md:p-6 space-y-6">
        @yield('content')
      </main>
      <x-dashboard.footer />
    </div>
  </div>

  <script>
    const $ = (sel, el = document) => el.querySelector(sel);
    const $$ = (sel, el = document) => Array.from(el.querySelectorAll(sel));
    const html = document.documentElement;
    const sidebar = $('#sidebar');
    const overlay = $('#overlay');
    const btnSidebar = $('#btn-toggle-sidebar');
    const btnTheme = $('#btn-toggle-theme');
    const THEME_KEY = 'dash_theme';
    const applySavedTheme = () => {
      const saved = localStorage.getItem(THEME_KEY);
      if (saved === 'dark') html.classList.add('dark');
      if (saved === 'light') html.classList.remove('dark');
    };
    const toggleTheme = () => {
      html.classList.toggle('dark');
      localStorage.setItem(THEME_KEY, html.classList.contains('dark') ? 'dark' : 'light');
    };
    applySavedTheme();
    btnTheme?.addEventListener('click', toggleTheme);
    const openSidebar = () => {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      btnSidebar?.setAttribute('aria-expanded', 'true');
    };
    const closeSidebar = () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      btnSidebar?.setAttribute('aria-expanded', 'false');
    };
    btnSidebar?.addEventListener('click', () => {
      const isClosed = sidebar.classList.contains('-translate-x-full');
      isClosed ? openSidebar() : closeSidebar();
    });
    overlay?.addEventListener('click', closeSidebar);
    const media = window.matchMedia('(min-width: 768px)');
    const handleMedia = (e) => { if (e.matches) overlay.classList.add('hidden'); };
    media.addEventListener('change', handleMedia);
  </script>
  @stack('scripts')
</body>
</html>