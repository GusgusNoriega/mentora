<aside id="sidebar" data-component="Sidebar"
  class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full md:translate-x-0 transform transition-transform duration-300 ease-in-out md:sticky md:top-0 md:h-[100dvh] overflow-y-auto md:z-auto card border border-white/10 md:border-0">
  <!-- [SIDEBAR: Header de perfil] -->
  <div class="p-4 border-b border-white/10 flex items-center gap-3">
    <img src="https://i.pravatar.cc/96?img=12" alt="Avatar" class="h-12 w-12 rounded-full object-cover" />
    <div>
      <p class="font-semibold leading-5">Tu Nombre</p>
      <p class="text-sm text-muted">Administrador</p>
    </div>
  </div>

  <!-- [SIDEBAR: Navegación] -->
  <nav class="p-4">
    <ul class="space-y-1" role="list">
      <li>
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 13h8V3H3zM13 21h8v-8h-8zM13 3h8v6h-8zM3 21h8v-6H3z"/></svg>
          <span class="font-medium">Dashboard</span>
        </a>
      </li>
      <li>
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 12l-10 7L2 12l10-7 10 7z"/><path d="M2 12l10 7 10-7"/><path d="M2 12l10-7 10 7"/><path d="M12 19V5"/></svg>
          <span class="font-medium">Cursos</span>
        </a>
      </li>
      <li>
        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <span class="font-medium">Usuarios</span>
        </a>
      </li>
      <li>
        <a href="{{ route('categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
          <span class="font-medium">Categorías</span>
        </a>
      </li>
      <li>
        <a href="{{ route('tags.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
          <span class="font-medium">Etiquetas</span>
        </a>
      </li>
      <li>
        <a href="{{ route('rbac.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
          </svg>
          <span class="font-medium">Roles &amp; Permisos</span>
        </a>
      </li>
      <li>
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 5 15.4a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.36 0 .7.07 1.01.2H21a2 2 0 1 1 0 4h-.09c-.31.13-.65.2-1.01.2z"/></svg>
          <span class="font-medium">Ajustes</span>
        </a>
      </li>
      <li>
        <a href="{{ route('media.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/></svg>
          <span class="font-medium">Media</span>
        </a>
      </li>
      <li>
        <a href="{{ route('media.manager') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition">
          <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
          <span class="font-medium">Media Manager</span>
        </a>
      </li>
      <li>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-brand-100 dark:hover:bg-white/5 transition text-left">
            <svg class="h-5 w-5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            <span class="font-medium">Salir</span>
          </button>
        </form>
      </li>
    </ul>
  </nav>

  <!-- [SIDEBAR: Pie/acciones] -->
  <div class="mt-auto p-4 border-t border-white/10">
    <button id="btn-toggle-theme" type="button" class="w-full inline-flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-medium bg-brand-500 hover:bg-brand-600 text-white focus:outline-none focus:ring-2 focus:ring-ring/80">
      <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v1"/><path d="M12 20v1"/><path d="M3 12h1"/><path d="M20 12h1"/><path d="M18.364 5.636l-.707.707"/><path d="M6.343 17.657l-.707.707"/><path d="M5.636 5.636l.707.707"/><path d="M17.657 17.657l.707.707"/><path d="M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z"/></svg>
      Tema claro/oscuro
    </button>
  </div>
</aside>