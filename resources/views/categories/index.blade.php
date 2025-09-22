@extends('layouts.dashboard')

@section('title', 'Categorías • Mentora')

@section('content')
  <div id="categories-page-container" class="">
    <!-- Encabezado -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Categorías</h1>
        <p class="text-sm text-muted mt-1">Gestión de categorías y subcategorías. Respeta el modo oscuro del layout.</p>
      </div>
      <div class="flex items-center gap-2">
        <button id="categories-open-create" class="hidden md:inline-flex items-center gap-2 rounded-lg bg-brand-600 text-white px-4 py-2 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-ring">
          <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
          Nueva categoría
        </button>
      </div>
    </div>

    <!-- Controles de búsqueda -->
    <div id="categories-filters" class="card rounded-xl2 p-4 md:p-5">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <label class="block">
          <span class="text-sm text-muted">Buscar</span>
          <input id="categories-search-input" type="text" placeholder="Nombre de categoría" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
        </label>
        <label class="block">
          <span class="text-sm text-muted">Categoría padre (opcional)</span>
          <select id="categories-parent-input" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
            <option value="">Todas</option>
            <!-- Opciones se cargarán dinámicamente -->
          </select>
        </label>
        <div class="flex items-end gap-2">
          <button id="categories-btn-search" class="w-full md:w-auto rounded-lg bg-brand-600 text-white px-4 py-2 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-ring">Aplicar</button>
          <button id="categories-btn-clear" class="w-full md:w-auto rounded-lg border border-gray-200 dark:border-slate-700 px-4 py-2 hover:bg-gray-50 dark:hover:bg-slate-700/40 focus:outline-none focus:ring-2 focus:ring-ring">Limpiar</button>
        </div>
      </div>
    </div>

    <!-- Tabla de categorías -->
    <div id="categories-section" class="card rounded-xl2 overflow-hidden mt-5">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 dark:bg-slate-800">
            <tr class="text-left">
              <th class="px-4 py-3 font-medium text-muted">ID</th>
              <th class="px-4 py-3 font-medium text-muted">Nombre</th>
              <th class="px-4 py-3 font-medium text-muted">Slug</th>
              <th class="px-4 py-3 font-medium text-muted">Categoría padre</th>
              <th class="px-4 py-3 font-medium text-muted">Cursos</th>
              <th class="px-4 py-3 font-medium text-muted text-right">Acciones</th>
            </tr>
          </thead>
          <tbody id="categories-table-body" class="divide-y divide-gray-100 dark:divide-slate-700">
            <!-- filas -->
          </tbody>
        </table>
      </div>
      <div id="categories-pagination" class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-slate-800">
        <!-- paginación -->
      </div>
    </div>

    <!-- Mensajes -->
    <div id="categories-empty-state" class="hidden">
      <div class="card rounded-xl2 p-8 text-center">
        <p class="text-muted">No se encontraron resultados.</p>
      </div>
    </div>

    <!-- Toast -->
    <div id="categories-toast" class="fixed bottom-4 right-4 z-40 hidden">
      <div class="rounded-lg bg-card shadow-soft border border-gray-200 dark:border-slate-700 px-4 py-3">
        <p id="categories-toast-text" class="text-sm"></p>
      </div>
    </div>

    <!-- Modal crear/editar -->
    <div id="categories-modal" class="fixed inset-0 z-40 hidden" aria-modal="true" role="dialog">
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
      <div class="relative mx-auto mt-16 w-[95%] max-w-lg">
        <div class="card rounded-xl2">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 id="categories-modal-title" class="text-lg font-semibold">Nueva categoría</h3>
            <button id="categories-close-modal" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-slate-700/50" aria-label="Cerrar">
              <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 111.414 1.415L11.414 10l4.95 4.95a1 1 0 11-1.414 1.415L10 11.414l-4.95 4.95a1 1 0 11-1.415-1.415L8.586 10l-4.95-4.95A1 1 0 115.05 3.636L10 8.586z" clip-rule="evenodd"/></svg>
            </button>
          </div>
          <form id="categories-form" class="px-5 py-4 space-y-3">
            <div class="grid grid-cols-1 gap-3">
              <label class="block">
                <span class="text-sm text-muted">Nombre</span>
                <input id="categories-name" type="text" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring" required>
              </label>
              <label class="block">
                <span class="text-sm text-muted">Slug (opcional)</span>
                <input id="categories-slug" type="text" placeholder="Se genera automáticamente" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
              </label>
              <label class="block">
                <span class="text-sm text-muted">Categoría padre (opcional)</span>
                <select id="categories-parent-id" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
                  <option value="">Ninguna (categoría raíz)</option>
                  <!-- Opciones se cargarán dinámicamente -->
                </select>
              </label>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
              <button type="button" id="categories-cancel" class="rounded-lg border border-gray-200 dark:border-slate-700 px-4 py-2 hover:bg-gray-50 dark:hover:bg-slate-700/40 focus:outline-none focus:ring-2 focus:ring-ring">Cancelar</button>
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
  const CATEGORIES_BASE = `${API_BASE}/categories`;

  const state = {
    page: 1,
    lastPage: 1,
    perPage: 15,
    total: 0,
    search: '',
    parent_id: '',
    allCategories: [] // Para selects
  };

  // Referencias DOM
  const refs = {
    section: byId('categories-section'),
    tableBody: byId('categories-table-body'),
    pagination: byId('categories-pagination'),
    emptyState: byId('categories-empty-state'),
    btnOpenCreate: byId('categories-open-create'),
    searchInput: byId('categories-search-input'),
    parentInput: byId('categories-parent-input'),
    btnApply: byId('categories-btn-search'),
    btnClear: byId('categories-btn-clear'),
    modal: byId('categories-modal'),
    modalTitle: byId('categories-modal-title'),
    btnCloseModal: byId('categories-close-modal'),
    btnCancel: byId('categories-cancel'),
    form: byId('categories-form'),
    fName: byId('categories-name'),
    fSlug: byId('categories-slug'),
    fParentId: byId('categories-parent-id'),
    toast: byId('categories-toast'),
    toastText: byId('categories-toast-text'),
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

  function openModal(mode = 'create', category = null) {
    refs.form.dataset.mode = mode;
    refs.form.dataset.categoryId = category?.id || '';
    refs.modalTitle.textContent = mode === 'edit' ? 'Editar categoría' : 'Nueva categoría';

    refs.fName.value = category?.name || '';
    refs.fSlug.value = category?.slug || '';
    refs.fParentId.value = category?.parent_id || '';

    refs.modal.classList.remove('hidden');
  }
  function closeModal() {
    refs.modal.classList.add('hidden');
  }

  function renderCategoriesTable(items = []) {
    refs.tableBody.innerHTML = '';
    if (!items.length) {
      refs.emptyState.classList.remove('hidden');
      return;
    }
    refs.emptyState.classList.add('hidden');

    const rows = items.map(c => {
      const parentName = c.parent ? c.parent.name : '-';
      const coursesCount = c.courses_count || 0;
      return `
        <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-800/60">
          <td class="px-4 py-3 whitespace-nowrap">${c.id}</td>
          <td class="px-4 py-3">${c.name}</td>
          <td class="px-4 py-3">${c.slug}</td>
          <td class="px-4 py-3">${parentName}</td>
          <td class="px-4 py-3">${coursesCount}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2 justify-end">
              <button class="categories-btn-edit rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-700/40" data-id="${c.id}">Editar</button>
              <button class="categories-btn-delete rounded bg-red-600 text-white px-3 py-1.5 hover:bg-red-700" data-id="${c.id}">Eliminar</button>
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
        <button class="categories-page-prev rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 ${prevDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-slate-700/40'}" ${prevDisabled ? 'disabled' : ''}>Anterior</button>
        <span class="text-sm">Página ${current_page} de ${last_page}</span>
        <button class="categories-page-next rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 ${nextDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-slate-700/40'}" ${nextDisabled ? 'disabled' : ''}>Siguiente</button>
      </div>
    `;
  }

  function renderParentSelects() {
    const options = state.allCategories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    refs.parentInput.innerHTML = '<option value="">Todas</option>' + options;
    refs.fParentId.innerHTML = '<option value="">Ninguna (categoría raíz)</option>' + options;
  }

  async function loadCategories(page = 1) {
    const params = new URLSearchParams();
    if (state.search) params.set('search', state.search);
    if (state.parent_id) params.set('parent_id', state.parent_id);
    params.set('page', page);

    try {
      const res = await apiFetch(`${CATEGORIES_BASE}?${params.toString()}`);
      const paginator = res.data;
      renderCategoriesTable(paginator.data || []);
      renderPagination(paginator);
    } catch (err) {
      if (err.status === 401) {
        showToast('Sesión/API token inválido. Inicia sesión nuevamente.');
      } else {
        showToast(err.message || 'Error al cargar categorías.');
      }
    }
  }

  async function loadAllCategories() {
    try {
      const res = await apiFetch(`${CATEGORIES_BASE}?per_page=1000`); // Cargar todas para selects
      state.allCategories = res.data.data || [];
      renderParentSelects();
    } catch (err) {
      console.error('Error cargando todas las categorías:', err);
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
      const editBtn = e.target.closest('.categories-btn-edit');
      const delBtn = e.target.closest('.categories-btn-delete');
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
      if (e.target.classList.contains('categories-page-prev') && state.page > 1) {
        loadCategories(state.page - 1);
      }
      if (e.target.classList.contains('categories-page-next') && state.page < state.lastPage) {
        loadCategories(state.page + 1);
      }
    });

    // Filtros
    const applyFilters = () => {
      state.search = refs.searchInput.value.trim();
      state.parent_id = refs.parentInput.value;
      loadCategories(1);
    };
    refs.btnApply?.addEventListener('click', applyFilters);
    refs.btnClear?.addEventListener('click', () => {
      refs.searchInput.value = '';
      refs.parentInput.value = '';
      state.search = '';
      state.parent_id = '';
      loadCategories(1);
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
        slug: refs.fSlug.value.trim() || undefined,
        parent_id: refs.fParentId.value || undefined
      };

      try {
        if (mode === 'create') {
          await apiFetch(CATEGORIES_BASE, { method: 'POST', body: JSON.stringify(payload) });
          closeModal();
          showToast('Categoría creada correctamente.');
          loadCategories(1);
          loadAllCategories(); // Recargar selects
        } else {
          const id = refs.form.dataset.categoryId;
          await apiFetch(`${CATEGORIES_BASE}/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
          closeModal();
          showToast('Categoría actualizada.');
          loadCategories(state.page);
          loadAllCategories(); // Recargar selects
        }
      } catch (err) {
        showToast(err.message || 'Error al guardar categoría.');
        console.error(err);
      }
    });
  }

  async function handleEdit(id) {
    try {
      const res = await apiFetch(`${CATEGORIES_BASE}/${id}`);
      const category = res.data;
      openModal('edit', category);
    } catch (err) {
      showToast(err.message || 'No se pudo obtener la categoría.');
    }
  }

  async function handleDelete(id) {
    const ok = confirm('¿Eliminar esta categoría? Esta acción no se puede deshacer.');
    if (!ok) return;
    try {
      await apiFetch(`${CATEGORIES_BASE}/${id}`, { method: 'DELETE' });
      showToast('Categoría eliminada.');
      const nextPage = state.page > 1 && refs.tableBody.children.length === 1 ? state.page - 1 : state.page;
      loadCategories(nextPage);
      loadAllCategories(); // Recargar selects
    } catch (err) {
      showToast(err.message || 'No se pudo eliminar la categoría.');
    }
  }

  // Init
  (async function init() {
    mountEvents();
    await loadAllCategories();
    await loadCategories(1);
  })();
})();
</script>
@endpush