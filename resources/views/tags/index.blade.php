@extends('layouts.dashboard')

@section('title', 'Etiquetas • Mentora')

@section('content')
  <div id="tags-page-container" class="">
    <!-- Encabezado -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Etiquetas</h1>
        <p class="text-sm text-muted mt-1">Gestión de etiquetas. Respeta el modo oscuro del layout.</p>
      </div>
      <div class="flex items-center gap-2">
        <button id="tags-open-create" class="hidden md:inline-flex items-center gap-2 rounded-lg bg-brand-600 text-white px-4 py-2 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-ring">
          <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
          Nueva etiqueta
        </button>
      </div>
    </div>

    <!-- Controles de búsqueda -->
    <div id="tags-filters" class="card rounded-xl2 p-4 md:p-5">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <label class="block">
          <span class="text-sm text-muted">Buscar</span>
          <input id="tags-search-input" type="text" placeholder="Nombre de etiqueta" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
        </label>
        <div class="flex items-end gap-2">
          <button id="tags-btn-search" class="w-full md:w-auto rounded-lg bg-brand-600 text-white px-4 py-2 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-ring">Aplicar</button>
          <button id="tags-btn-clear" class="w-full md:w-auto rounded-lg border border-gray-200 dark:border-slate-700 px-4 py-2 hover:bg-gray-50 dark:hover:bg-slate-700/40 focus:outline-none focus:ring-2 focus:ring-ring">Limpiar</button>
        </div>
      </div>
    </div>

    <!-- Tabla de etiquetas -->
    <div id="tags-section" class="card rounded-xl2 overflow-hidden mt-5">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 dark:bg-slate-800">
            <tr class="text-left">
              <th class="px-4 py-3 font-medium text-muted">ID</th>
              <th class="px-4 py-3 font-medium text-muted">Nombre</th>
              <th class="px-4 py-3 font-medium text-muted">Slug</th>
              <th class="px-4 py-3 font-medium text-muted">Cursos</th>
              <th class="px-4 py-3 font-medium text-muted text-right">Acciones</th>
            </tr>
          </thead>
          <tbody id="tags-table-body" class="divide-y divide-gray-100 dark:divide-slate-700">
            <!-- filas -->
          </tbody>
        </table>
      </div>
      <div id="tags-pagination" class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-slate-800">
        <!-- paginación -->
      </div>
    </div>

    <!-- Mensajes -->
    <div id="tags-empty-state" class="hidden">
      <div class="card rounded-xl2 p-8 text-center">
        <p class="text-muted">No se encontraron resultados.</p>
      </div>
    </div>

    <!-- Toast -->
    <div id="tags-toast" class="fixed bottom-4 right-4 z-40 hidden">
      <div class="rounded-lg bg-card shadow-soft border border-gray-200 dark:border-slate-700 px-4 py-3">
        <p id="tags-toast-text" class="text-sm"></p>
      </div>
    </div>

    <!-- Modal crear/editar -->
    <div id="tags-modal" class="fixed inset-0 z-40 hidden" aria-modal="true" role="dialog">
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
      <div class="relative mx-auto mt-16 w-[95%] max-w-lg">
        <div class="card rounded-xl2">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 id="tags-modal-title" class="text-lg font-semibold">Nueva etiqueta</h3>
            <button id="tags-close-modal" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-slate-700/50" aria-label="Cerrar">
              <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 111.414 1.415L11.414 10l4.95 4.95a1 1 0 11-1.414 1.415L10 11.414l-4.95 4.95a1 1 0 11-1.415-1.415L8.586 10l-4.95-4.95A1 1 0 115.05 3.636L10 8.586z" clip-rule="evenodd"/></svg>
            </button>
          </div>
          <form id="tags-form" class="px-5 py-4 space-y-3">
            <div class="grid grid-cols-1 gap-3">
              <label class="block">
                <span class="text-sm text-muted">Nombre</span>
                <input id="tags-name" type="text" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring" required>
              </label>
              <label class="block">
                <span class="text-sm text-muted">Slug (opcional)</span>
                <input id="tags-slug" type="text" placeholder="Se genera automáticamente" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
              </label>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
              <button type="button" id="tags-cancel" class="rounded-lg border border-gray-200 dark:border-slate-700 px-4 py-2 hover:bg-gray-50 dark:hover:bg-slate-700/40 focus:outline-none focus:ring-2 focus:ring-ring">Cancelar</button>
              <button class="rounded-lg bg-brand-600 text-white px-4 py-2 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-ring">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
(() => {
  // Utilidades
  const $ = (sel, el = document) => el.querySelector(sel);
  const $$ = (sel, el = document) => Array.from(el.querySelectorAll(sel));
  const byId = (id) => document.getElementById(id);

  // Token desde layout (head)
  const TOKEN = document.querySelector('meta[name="api-token"]')?.content || '';
  const API_BASE = '/mentora/public/api';
  const TAGS_BASE = `${API_BASE}/tags`;

  const state = {
    page: 1,
    lastPage: 1,
    perPage: 15,
    total: 0,
    search: ''
  };

  // Referencias DOM
  const refs = {
    section: byId('tags-section'),
    tableBody: byId('tags-table-body'),
    pagination: byId('tags-pagination'),
    emptyState: byId('tags-empty-state'),
    btnOpenCreate: byId('tags-open-create'),
    searchInput: byId('tags-search-input'),
    btnApply: byId('tags-btn-search'),
    btnClear: byId('tags-btn-clear'),
    modal: byId('tags-modal'),
    modalTitle: byId('tags-modal-title'),
    btnCloseModal: byId('tags-close-modal'),
    btnCancel: byId('tags-cancel'),
    form: byId('tags-form'),
    fName: byId('tags-name'),
    fSlug: byId('tags-slug'),
    toast: byId('tags-toast'),
    toastText: byId('tags-toast-text'),
  };

  const showToast = (msg, timeout = 3000) => {
    refs.toastText.textContent = msg;
    refs.toast.classList.remove('hidden');
    setTimeout(() => refs.toast.classList.add('hidden'), timeout);
  };

  const headers = () => ({
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    ...(TOKEN ? { 'Authorization': `Bearer ${TOKEN}` } : {})
  });

  async function apiFetch(url, options = {}) {
    const res = await fetch(url, {
      ...options,
      headers: { ...headers(), ...(options.headers || {}) }
    });
    let payload = null;
    try { payload = await res.json(); } catch (e) { /* no-op */ }
    if (!res.ok) {
      const msg = payload?.meta?.message || payload?.message || `Error HTTP ${res.status}`;
      const errors = payload?.meta?.errors || payload?.errors;
      throw { status: res.status, message: msg, errors, raw: payload };
    }
    return payload;
  }

  function openModal(mode = 'create', tag = null) {
    refs.form.dataset.mode = mode;
    refs.form.dataset.tagId = tag?.id || '';
    refs.modalTitle.textContent = mode === 'edit' ? 'Editar etiqueta' : 'Nueva etiqueta';

    refs.fName.value = tag?.name || '';
    refs.fSlug.value = tag?.slug || '';

    refs.modal.classList.remove('hidden');
  }
  function closeModal() {
    refs.modal.classList.add('hidden');
  }

  function renderTagsTable(items = []) {
    refs.tableBody.innerHTML = '';
    if (!items.length) {
      refs.emptyState.classList.remove('hidden');
      return;
    }
    refs.emptyState.classList.add('hidden');

    const rows = items.map(t => {
      const coursesCount = t.courses_count || 0;
      return `
        <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-800/60">
          <td class="px-4 py-3 whitespace-nowrap">${t.id}</td>
          <td class="px-4 py-3">${t.name}</td>
          <td class="px-4 py-3">${t.slug}</td>
          <td class="px-4 py-3">${coursesCount}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2 justify-end">
              <button class="tags-btn-edit rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-700/40" data-id="${t.id}">Editar</button>
              <button class="tags-btn-delete rounded bg-red-600 text-white px-3 py-1.5 hover:bg-red-700" data-id="${t.id}">Eliminar</button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
    refs.tableBody.innerHTML = rows;
  }

  function renderPagination(meta) {
    const { current_page, last_page, total } = meta;
    state.page = current_page;
    state.lastPage = last_page;
    state.total = total;

    const prevDisabled = current_page <= 1;
    const nextDisabled = current_page >= last_page;

    refs.pagination.innerHTML = `
      <div class="text-sm text-muted">Total: ${total}</div>
      <div class="flex items-center gap-2">
        <button class="tags-page-prev rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 ${prevDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-slate-700/40'}" ${prevDisabled ? 'disabled' : ''}>Anterior</button>
        <span class="text-sm">Página ${current_page} de ${last_page}</span>
        <button class="tags-page-next rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 ${nextDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-slate-700/40'}" ${nextDisabled ? 'disabled' : ''}>Siguiente</button>
      </div>
    `;
  }

  async function loadTags(page = 1) {
    const params = new URLSearchParams();
    if (state.search) params.set('search', state.search);
    params.set('page', page);

    try {
      const res = await apiFetch(`${TAGS_BASE}?${params.toString()}`);
      const paginator = res.data;
      renderTagsTable(paginator.data || []);
      renderPagination(paginator);
    } catch (err) {
      if (err.status === 401) {
        showToast('Sesión/API token inválido. Inicia sesión nuevamente.');
      } else {
        showToast(err.message || 'Error al cargar etiquetas.');
      }
    }
  }

  // Eventos
  function mountEvents() {
    // Crear
    refs.btnOpenCreate?.addEventListener('click', () => openModal('create'));

    // Cerrar modal
    refs.btnCloseModal?.addEventListener('click', closeModal);
    refs.btnCancel?.addEventListener('click', closeModal);
    refs.modal?.addEventListener('click', (e) => {
      if (e.target === refs.modal) closeModal();
    });

    // Acciones tabla (delegación)
    refs.tableBody?.addEventListener('click', (e) => {
      const editBtn = e.target.closest('.tags-btn-edit');
      const delBtn = e.target.closest('.tags-btn-delete');
      if (editBtn) {
        const id = editBtn.dataset.id;
        handleEdit(id);
      }
      if (delBtn) {
        const id = delBtn.dataset.id;
        handleDelete(id);
      }
    });

    // Paginación
    refs.pagination?.addEventListener('click', (e) => {
      if (e.target.classList.contains('tags-page-prev') && state.page > 1) {
        loadTags(state.page - 1);
      }
      if (e.target.classList.contains('tags-page-next') && state.page < state.lastPage) {
        loadTags(state.page + 1);
      }
    });

    // Filtros
    const applyFilters = () => {
      state.search = refs.searchInput.value.trim();
      loadTags(1);
    };
    refs.btnApply?.addEventListener('click', applyFilters);
    refs.btnClear?.addEventListener('click', () => {
      refs.searchInput.value = '';
      state.search = '';
      loadTags(1);
    });

    // Enter en inputs
    [refs.searchInput].forEach(inp => {
      inp?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          applyFilters();
        }
      });
    });

    // Submit modal (crear/editar)
    refs.form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const mode = refs.form.dataset.mode || 'create';
      const payload = {
        name: refs.fName.value.trim(),
        slug: refs.fSlug.value.trim() || undefined
      };

      try {
        if (mode === 'create') {
          await apiFetch(TAGS_BASE, { method: 'POST', body: JSON.stringify(payload) });
          closeModal();
          showToast('Etiqueta creada correctamente.');
          loadTags(1);
        } else {
          const id = refs.form.dataset.tagId;
          await apiFetch(`${TAGS_BASE}/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
          closeModal();
          showToast('Etiqueta actualizada.');
          loadTags(state.page);
        }
      } catch (err) {
        showToast(err.message || 'Error al guardar etiqueta.');
        console.error(err);
      }
    });
  }

  async function handleEdit(id) {
    try {
      const res = await apiFetch(`${TAGS_BASE}/${id}`);
      const tag = res.data;
      openModal('edit', tag);
    } catch (err) {
      showToast(err.message || 'No se pudo obtener la etiqueta.');
    }
  }

  async function handleDelete(id) {
    const ok = confirm('¿Eliminar esta etiqueta? Esta acción no se puede deshacer.');
    if (!ok) return;
    try {
      await apiFetch(`${TAGS_BASE}/${id}`, { method: 'DELETE' });
      showToast('Etiqueta eliminada.');
      const nextPage = state.page > 1 && refs.tableBody.children.length === 1 ? state.page - 1 : state.page;
      loadTags(nextPage);
    } catch (err) {
      showToast(err.message || 'No se pudo eliminar la etiqueta.');
    }
  }

  // Init
  (async function init() {
    mountEvents();
    await loadTags(1);
  })();
})();
</script>
@endpush