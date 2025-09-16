@extends('layouts.dashboard')
@section('title', 'Dashboard — Mentora')

@section('content')
  <!-- [SECCIÓN: Título + Migas] -->
  <section data-component="PageHeader" class="flex flex-col gap-2">
    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Bienvenido de nuevo 👋</h1>
    <p class="text-muted">Resumen de tu actividad reciente y métricas principales.</p>
  </section>

  <!-- [SECCIÓN: Métricas rápidas] -->
  <section data-component="KPI" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
    <!-- Tarjeta 1 -->
    <article class="card rounded-2xl p-4">
      <div class="flex items-center justify-between">
        <h3 class="text-sm text-muted">Cursos activos</h3>
        <span class="inline-flex items-center rounded-full bg-brand-100 text-brand-700 text-xs font-medium px-2 py-1">+12%</span>
      </div>
      <p class="mt-2 text-3xl font-semibold">36</p>
      <p class="mt-1 text-xs text-muted">Actualizado hace 5 min</p>
    </article>
    <!-- Tarjeta 2 -->
    <article class="card rounded-2xl p-4">
      <div class="flex items-center justify-between">
        <h3 class="text-sm text-muted">Usuarios nuevos</h3>
        <span class="inline-flex items-center rounded-full bg-brand-100 text-brand-700 text-xs font-medium px-2 py-1">+5%</span>
      </div>
      <p class="mt-2 text-3xl font-semibold">124</p>
      <p class="mt-1 text-xs text-muted">Esta semana</p>
    </article>
    <!-- Tarjeta 3 -->
    <article class="card rounded-2xl p-4">
      <div class="flex items-center justify-between">
        <h3 class="text-sm text-muted">Tasa de finalización</h3>
        <span class="inline-flex items-center rounded-full bg-brand-100 text-brand-700 text-xs font-medium px-2 py-1">+2.4%</span>
      </div>
      <p class="mt-2 text-3xl font-semibold">78%</p>
      <p class="mt-1 text-xs text-muted">Últimos 30 días</p>
    </article>
    <!-- Tarjeta 4 -->
    <article class="card rounded-2xl p-4">
      <div class="flex items-center justify-between">
        <h3 class="text-sm text-muted">Ingresos</h3>
        <span class="inline-flex items-center rounded-full bg-brand-100 text-brand-700 text-xs font-medium px-2 py-1">+9.8%</span>
      </div>
      <p class="mt-2 text-3xl font-semibold">$4,820</p>
      <p class="mt-1 text-xs text-muted">Mes actual</p>
    </article>
  </section>

  <!-- [SECCIÓN: Grilla principal] -->
  <section data-component="MainGrid" class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    <!-- Columna 1-2: Actividad reciente -->
    <article class="card rounded-2xl p-0 xl:col-span-2 overflow-hidden">
      <header class="flex items-center justify-between p-4 border-b border-white/10">
        <h3 class="font-semibold">Actividad reciente</h3>
        <button class="text-sm text-brand-600 hover:underline">Ver todo</button>
      </header>
      <ul class="divide-y divide-white/10">
        <li class="p-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="h-2.5 w-2.5 rounded-full bg-accent"></span>
            <p class="text-sm"><span class="font-medium">María</span> completó <span class="font-medium">"React desde cero"</span></p>
          </div>
          <span class="text-xs text-muted">hace 2 h</span>
        </li>
        <li class="p-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="h-2.5 w-2.5 rounded-full bg-brand-500"></span>
            <p class="text-sm"><span class="font-medium">Nuevo curso</span> publicado: <span class="font-medium">"Laravel avanzado"</span></p>
          </div>
          <span class="text-xs text-muted">hoy</span>
        </li>
        <li class="p-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            <span class="h-2.5 w-2.5 rounded-full bg-brand-400"></span>
            <p class="text-sm"><span class="font-medium">4 usuarios</span> se suscribieron a <span class="font-medium">"Docker para devs"</span></p>
          </div>
          <span class="text-xs text-muted">ayer</span>
        </li>
      </ul>
    </article>

    <!-- Columna 3: Progreso -->
    <aside class="card rounded-2xl p-4">
      <h3 class="font-semibold">Tu progreso</h3>
      <ul class="mt-3 space-y-3">
        <li class="p-3 rounded-xl bg-brand-50 dark:bg-white/5">
          <div class="flex items-center justify-between text-sm">
            <span class="font-medium">API con Laravel</span>
            <span class="text-muted">62%</span>
          </div>
          <div class="mt-2 h-2 rounded-full bg-white/40 dark:bg-white/10">
            <div class="h-full rounded-full bg-brand-500" style="width:62%"></div>
          </div>
        </li>
        <li class="p-3 rounded-xl bg-brand-50 dark:bg-white/5">
          <div class="flex items-center justify-between text-sm">
            <span class="font-medium">Tailwind CSS</span>
            <span class="text-muted">85%</span>
          </div>
          <div class="mt-2 h-2 rounded-full bg-white/40 dark:bg-white/10">
            <div class="h-full rounded-full bg-brand-500" style="width:85%"></div>
          </div>
        </li>
        <li class="p-3 rounded-xl bg-brand-50 dark:bg-white/5">
          <div class="flex items-center justify-between text-sm">
            <span class="font-medium">Docker para devs</span>
            <span class="text-muted">30%</span>
          </div>
          <div class="mt-2 h-2 rounded-full bg-white/40 dark:bg-white/10">
            <div class="h-full rounded-full bg-brand-500" style="width:30%"></div>
          </div>
        </li>
      </ul>
    </aside>
  </section>

@endsection