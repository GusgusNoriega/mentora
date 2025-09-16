<header data-component="Topbar" class="sticky top-0 z-20 bg-surface/80 backdrop-blur supports-[backdrop-filter]:bg-surface/70 border-b border-white/10">
  <div class="px-4 md:px-6 py-3 flex items-center gap-3">
    <!-- Botón de menú (móvil) -->
    <button id="btn-toggle-sidebar" class="md:hidden inline-flex items-center justify-center rounded-lg p-2 hover:bg-brand-100 dark:hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-ring/80" aria-label="Abrir menú" aria-expanded="false">
      <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>

    <!-- Buscador -->
    <div class="relative flex-1 max-w-xl">
      <input type="text" placeholder="Buscar…" class="w-full rounded-xl border border-white/10 bg-card/70 py-2 pl-10 pr-3 text-sm focus:outline-none focus:ring-2 focus:ring-ring/80 placeholder:text-muted" />
      <svg class="pointer-events-none absolute left-3 top-2.5 h-5 w-5 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </div>

    <!-- Acciones rápidas -->
    <div class="hidden sm:flex items-center gap-2">
      <button class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium bg-card border border-white/10 hover:bg-white/50 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-ring/80">
        <span>+ Nuevo</span>
      </button>
      <button class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium bg-card border border-white/10 hover:bg-white/50 dark:hover:bg-white/5 focus:outline-none focus:ring-2 focus:ring-ring/80">
        Filtrar
      </button>
    </div>
  </div>
</header>