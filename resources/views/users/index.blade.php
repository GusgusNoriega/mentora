@extends('layouts.dashboard')

@section('title', 'Usuarios • Mentora')

@section('content')
  <div id="users-page-container">
    <!-- Encabezado -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-semibold tracking-tight">Usuarios</h1>
        <p class="text-sm text-muted mt-1">Gestión de usuarios y sus roles. Respeta el modo oscuro del layout.</p>
      </div>
      <div class="flex items-center gap-2">
        <button id="users-open-create" class="hidden md:inline-flex items-center gap-2 rounded-lg bg-brand-600 text-white px-4 py-2 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-ring">
          <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
          Nuevo usuario
        </button>
      </div>
    </div>

    <!-- Controles de búsqueda y filtros (solo admin) -->
    <div id="users-admin-filters" class="card rounded-xl2 p-4 md:p-5 hidden">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <label class="block">
          <span class="text-sm text-muted">Buscar</span>
          <input id="users-search-input" type="text" placeholder="Nombre o email" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
        </label>
        <label class="block">
          <span class="text-sm text-muted">Rol (opcional)</span>
          <input id="users-role-input" type="text" placeholder="admin, student, etc." class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
        </label>
        <div class="flex items-end gap-2">
          <button id="users-btn-search" class="w-full md:w-auto rounded-lg bg-brand-600 text-white px-4 py-2 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-ring">Aplicar</button>
          <button id="users-btn-clear" class="w-full md:w-auto rounded-lg border border-gray-200 dark:border-slate-700 px-4 py-2 hover:bg-gray-50 dark:hover:bg-slate-700/40 focus:outline-none focus:ring-2 focus:ring-ring">Limpiar</button>
        </div>
      </div>
    </div>

    <!-- Tabla de usuarios (solo admin) -->
    <div id="users-admin-section" class="card rounded-xl2 overflow-hidden hidden mt-5">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-50 dark:bg-slate-800">
            <tr class="text-left">
              <th class="px-4 py-3 font-medium text-muted">ID</th>
              <th class="px-4 py-3 font-medium text-muted">Nombre</th>
              <th class="px-4 py-3 font-medium text-muted">Email</th>
              <th class="px-4 py-3 font-medium text-muted">Roles</th>
              <th class="px-4 py-3 font-medium text-muted text-right">Acciones</th>
            </tr>
          </thead>
          <tbody id="users-table-body" class="divide-y divide-gray-100 dark:divide-slate-700">
            <!-- filas -->
          </tbody>
        </table>
      </div>
      <div id="users-pagination" class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-slate-800">
        <!-- paginación -->
      </div>
    </div>

    <!-- Vista alternativa para usuarios no admin: perfil -->
    <div id="users-nonadmin-section" class="hidden">
      <div class="card rounded-xl2 p-5">
        <h2 class="text-lg font-semibold">Mi perfil</h2>
        <p class="text-sm text-muted mt-1">Puedes actualizar tus datos básicos.</p>

        <form id="users-self-form" class="mt-4 space-y-3">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="block">
              <span class="text-sm text-muted">Nombre</span>
              <input id="users-self-name" type="text" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
            </label>
            <label class="block">
              <span class="text-sm text-muted">Email</span>
              <input id="users-self-email" type="email" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
            </label>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="block">
              <span class="text-sm text-muted">Nueva contraseña (opcional)</span>
              <input id="users-self-password" type="password" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
            </label>
            <label class="block">
              <span class="text-sm text-muted">Confirmación</span>
              <input id="users-self-password-confirm" type="password" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
            </label>
          </div>
          <div class="pt-2">
            <button class="rounded-lg bg-brand-600 text-white px-4 py-2 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-ring">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Mensajes -->
    <div id="users-empty-state" class="hidden">
      <div class="card rounded-xl2 p-8 text-center">
        <p class="text-muted">No se encontraron resultados.</p>
      </div>
    </div>

    <!-- Toast -->
    <div id="users-toast" class="fixed bottom-4 right-4 z-40 hidden">
      <div class="rounded-lg bg-card shadow-soft border border-gray-200 dark:border-slate-700 px-4 py-3">
        <p id="users-toast-text" class="text-sm"></p>
      </div>
    </div>

    <!-- Modal crear/editar -->
    <div id="users-modal" class="fixed inset-0 z-40 hidden" aria-modal="true" role="dialog">
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
      <div class="relative mx-auto mt-16 w-[95%] max-w-lg">
        <div class="card rounded-xl2">
          <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-slate-700">
            <h3 id="users-modal-title" class="text-lg font-semibold">Nuevo usuario</h3>
            <button id="users-close-modal" class="p-2 rounded hover:bg-gray-100 dark:hover:bg-slate-700/50" aria-label="Cerrar">
              <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 111.414 1.415L11.414 10l4.95 4.95a1 1 0 11-1.414 1.415L10 11.414l-4.95 4.95a1 1 0 11-1.415-1.415L8.586 10l-4.95-4.95A1 1 0 115.05 3.636L10 8.586z" clip-rule="evenodd"/></svg>
            </button>
          </div>
          <form id="users-form" class="px-5 py-4 space-y-3">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <label class="block">
                <span class="text-sm text-muted">Nombre</span>
                <input id="users-name" type="text" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring" required>
              </label>
              <label class="block">
                <span class="text-sm text-muted">Email</span>
                <input id="users-email" type="email" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring" required>
              </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <label class="block">
                <span class="text-sm text-muted">Contraseña</span>
                <input id="users-password" type="password" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
              </label>
              <label class="block">
                <span class="text-sm text-muted">Confirmación</span>
                <input id="users-password-confirm" type="password" class="mt-1 w-full rounded-lg border border-gray-200 dark:border-slate-700 bg-surface px-3 py-2 focus:outline-none focus:ring-2 focus:ring-ring">
              </label>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <label class="block">
                <span class="text-sm text-muted">Rol (opcional)</span>
                <x-role-select id="users-role" name="role" placeholder="Selecciona un rol" />
              </label>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
              <button type="button" id="users-cancel" class="rounded-lg border border-gray-200 dark:border-slate-700 px-4 py-2 hover:bg-gray-50 dark:hover:bg-slate-700/40 focus:outline-none focus:ring-2 focus:ring-ring">Cancelar</button>
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
  const ADMIN_USERS_BASE = `${API_BASE}/admin/users`;
  const USERS_BASE = `${API_BASE}/users`;

  const state = {
    isAdmin: false,
    profile: null,
    page: 1,
    lastPage: 1,
    perPage: 15,
    total: 0,
    search: '',
    role: ''
  };

  // Referencias DOM únicas para evitar conflictos
  const refs = {
    adminSection: byId('users-admin-section'),
    adminFilters: byId('users-admin-filters'),
    nonAdminSection: byId('users-nonadmin-section'),
    tableBody: byId('users-table-body'),
    pagination: byId('users-pagination'),
    emptyState: byId('users-empty-state'),
    btnOpenCreate: byId('users-open-create'),
    searchInput: byId('users-search-input'),
    roleInput: byId('users-role-input'),
    btnApply: byId('users-btn-search'),
    btnClear: byId('users-btn-clear'),
    modal: byId('users-modal'),
    modalTitle: byId('users-modal-title'),
    btnCloseModal: byId('users-close-modal'),
    btnCancel: byId('users-cancel'),
    form: byId('users-form'),
    fName: byId('users-name'),
    fEmail: byId('users-email'),
    fPassword: byId('users-password'),
    fPasswordConfirm: byId('users-password-confirm'),
    fRole: byId('users-role'),
    toast: byId('users-toast'),
    toastText: byId('users-toast-text'),
    selfForm: byId('users-self-form'),
    selfName: byId('users-self-name'),
    selfEmail: byId('users-self-email'),
    selfPassword: byId('users-self-password'),
    selfPasswordConfirm: byId('users-self-password-confirm'),
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

  function isAdminFromProfile(profile) {
    const roles = profile?.roles || [];
    return roles.some(r => (r.name || '').toLowerCase() === 'admin');
  }

  function setAdminUI(enabled) {
    state.isAdmin = !!enabled;
    refs.adminSection.classList.toggle('hidden', !enabled);
    refs.adminFilters.classList.toggle('hidden', !enabled);
    refs.btnOpenCreate?.classList.toggle('hidden', !enabled);
    refs.nonAdminSection.classList.toggle('hidden', enabled);
  }

  function openModal(mode = 'create', user = null) {
    refs.form.dataset.mode = mode;
    refs.form.dataset.userId = user?.id || '';
    refs.modalTitle.textContent = mode === 'edit' ? 'Editar usuario' : 'Nuevo usuario';

    refs.fName.value = user?.name || '';
    refs.fEmail.value = user?.email || '';
    refs.fPassword.value = '';
    refs.fPasswordConfirm.value = '';
    refs.fRole.value = (user?.roles?.[0]?.name) || '';

    refs.modal.classList.remove('hidden');
  }
  function closeModal() {
    refs.modal.classList.add('hidden');
  }

  function renderUsersTable(items = []) {
    refs.tableBody.innerHTML = '';
    if (!items.length) {
      refs.emptyState.classList.remove('hidden');
      return;
    }
    refs.emptyState.classList.add('hidden');

    const rows = items.map(u => {
      const roles = (u.roles || []).map(r => r.name).join(', ') || '-';
      return `
        <tr class="hover:bg-gray-50/60 dark:hover:bg-slate-800/60">
          <td class="px-4 py-3 whitespace-nowrap">${u.id}</td>
          <td class="px-4 py-3">${u.name}</td>
          <td class="px-4 py-3">${u.email}</td>
          <td class="px-4 py-3">${roles}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2 justify-end">
              <button class="users-btn-edit rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-slate-700/40" data-id="${u.id}">Editar</button>
              <button class="users-btn-delete rounded bg-red-600 text-white px-3 py-1.5 hover:bg-red-700" data-id="${u.id}">Eliminar</button>
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
        <button class="users-page-prev rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 ${prevDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-slate-700/40'}" ${prevDisabled ? 'disabled' : ''}>Anterior</button>
        <span class="text-sm">Página ${current_page} de ${last_page}</span>
        <button class="users-page-next rounded border border-gray-200 dark:border-slate-700 px-3 py-1.5 ${nextDisabled ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 dark:hover:bg-slate-700/40'}" ${nextDisabled ? 'disabled' : ''}>Siguiente</button>
      </div>
    `;
  }

  async function loadUsers(page = 1) {
    const params = new URLSearchParams();
    if (state.search) params.set('search', state.search);
    if (state.role) params.set('role', state.role);
    params.set('page', page);

    try {
      const res = await apiFetch(`${ADMIN_USERS_BASE}?${params.toString()}`);
      // res.data es el paginator laravel
      const paginator = res.data;
      renderUsersTable(paginator.data || []);
      renderPagination(paginator);
    } catch (err) {
      if (err.status === 403) {
        // No admin: mostrar vista de perfil
        showToast('No tienes permisos de administrador para listar usuarios.');
        setAdminUI(false);
        renderProfileSection();
      } else if (err.status === 401) {
        showToast('Sesión/API token inválido. Inicia sesión nuevamente.');
      } else {
        showToast(err.message || 'Error al cargar usuarios.');
      }
    }
  }

  function renderProfileSection() {
    if (!state.profile) return;
    refs.nonAdminSection.classList.remove('hidden');
    refs.selfName.value = state.profile.name || '';
    refs.selfEmail.value = state.profile.email || '';
  }

  async function loadProfile() {
    if (!TOKEN) {
      showToast('No se encontró token API en el layout. Inicia sesión.');
      return null;
    }
    try {
      const res = await apiFetch(`${USERS_BASE}/profile`);
      state.profile = res.data;
      return state.profile;
    } catch (err) {
      if (err.status === 401) {
        showToast('No autorizado. Inicia sesión.');
      } else {
        showToast(err.message || 'Error al cargar perfil.');
      }
      return null;
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
      const editBtn = e.target.closest('.users-btn-edit');
      const delBtn = e.target.closest('.users-btn-delete');
      if (editBtn) {
        const id = editBtn.dataset.id;
        // Buscar el usuario en la fila actual (mejor pedir al API show para datos frescos)
        handleEdit(id);
      }
      if (delBtn) {
        const id = delBtn.dataset.id;
        handleDelete(id);
      }
    });

    // Paginación
    refs.pagination?.addEventListener('click', (e) => {
      if (e.target.classList.contains('users-page-prev') && state.page > 1) {
        loadUsers(state.page - 1);
      }
      if (e.target.classList.contains('users-page-next') && state.page < state.lastPage) {
        loadUsers(state.page + 1);
      }
    });

    // Filtros
    const applyFilters = () => {
      state.search = refs.searchInput.value.trim();
      state.role = refs.roleInput.value.trim();
      loadUsers(1);
    };
    refs.btnApply?.addEventListener('click', applyFilters);
    refs.btnClear?.addEventListener('click', () => {
      refs.searchInput.value = '';
      refs.roleInput.value = '';
      state.search = '';
      state.role = '';
      loadUsers(1);
    });

    // Enter en inputs
    [refs.searchInput, refs.roleInput].forEach(inp => {
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
        email: refs.fEmail.value.trim()
      };
      const pass = refs.fPassword.value;
      const pass2 = refs.fPasswordConfirm.value;
      if (mode === 'create') {
        if (pass.length < 8) {
          showToast('La contraseña debe tener al menos 8 caracteres.');
          return;
        }
        if (pass !== pass2) {
          showToast('Las contraseñas no coinciden.');
          return;
        }
        payload.password = pass;
        payload.password_confirmation = pass2;
        const roleVal = refs.fRole.value.trim();
        if (roleVal) payload.role = roleVal;
        try {
          await apiFetch(ADMIN_USERS_BASE, { method: 'POST', body: JSON.stringify(payload) });
          closeModal();
          showToast('Usuario creado correctamente.');
          loadUsers(1);
        } catch (err) {
          showToast(err.message || 'Error al crear usuario.');
          console.error(err);
        }
      } else {
        const id = refs.form.dataset.userId;
        // Update parcial: solo enviar campos presentes
        const upd = {};
        if (payload.name) upd.name = payload.name;
        if (payload.email) upd.email = payload.email;
        if (pass) {
          if (pass.length < 8) {
            showToast('La contraseña debe tener al menos 8 caracteres.');
            return;
          }
          if (pass !== pass2) {
            showToast('Las contraseñas no coinciden.');
            return;
          }
          upd.password = pass;
          upd.password_confirmation = pass2;
        }
        // Incluir rol si fue especificado en el modal (solo admins ven este modal)
        const roleVal = refs.fRole.value.trim();
        if (roleVal) {
          upd.role = roleVal;
        }
        try {
          await apiFetch(`${USERS_BASE}/${id}`, { method: 'PUT', body: JSON.stringify(upd) });
          closeModal();
          showToast('Usuario actualizado.');
          // Si admin refrescar lista; si no admin, refrescar perfil
          if (state.isAdmin) {
            loadUsers(state.page);
          } else {
            const profile = await loadProfile();
            if (profile) renderProfileSection();
          }
        } catch (err) {
          showToast(err.message || 'Error al actualizar usuario.');
          console.error(err);
        }
      }
    });

    // Perfil (no admin) guardar
    refs.selfForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!state.profile) return;
      const upd = {
        name: refs.selfName.value.trim(),
        email: refs.selfEmail.value.trim()
      };
      const pass = refs.selfPassword.value;
      const pass2 = refs.selfPasswordConfirm.value;
      if (pass) {
        if (pass.length < 8) return showToast('La contraseña debe tener al menos 8 caracteres.');
        if (pass !== pass2) return showToast('Las contraseñas no coinciden.');
        upd.password = pass;
        upd.password_confirmation = pass2;
      }
      try {
        await apiFetch(`${USERS_BASE}/${state.profile.id}`, { method: 'PUT', body: JSON.stringify(upd) });
        showToast('Perfil actualizado.');
        refs.selfPassword.value = '';
        refs.selfPasswordConfirm.value = '';
        await loadProfile();
        renderProfileSection();
      } catch (err) {
        showToast(err.message || 'Error al actualizar perfil.');
      }
    });
  }

  async function handleEdit(id) {
    try {
      const res = await apiFetch(`${USERS_BASE}/${id}`);
      const user = res.data;
      openModal('edit', user);
    } catch (err) {
      showToast(err.message || 'No se pudo obtener el usuario.');
    }
  }

  async function handleDelete(id) {
    const ok = confirm('¿Eliminar este usuario? Esta acción no se puede deshacer.');
    if (!ok) return;
    try {
      await apiFetch(`${ADMIN_USERS_BASE}/${id}`, { method: 'DELETE' });
      showToast('Usuario eliminado.');
      const nextPage = state.page > 1 && refs.tableBody.children.length === 1 ? state.page - 1 : state.page;
      loadUsers(nextPage);
    } catch (err) {
      showToast(err.message || 'No se pudo eliminar el usuario.');
    }
  }

  // Init
  (async function init() {
    mountEvents();

    const profile = await loadProfile();
    if (!profile) return;

    const userIsAdmin = isAdminFromProfile(profile);
    setAdminUI(userIsAdmin);

    if (userIsAdmin) {
      await loadUsers(1);
    } else {
      renderProfileSection();
    }
  })();
})();
</script>
@endpush