@extends('layouts.dashboard')
@section('title', 'Roles & Permisos — Mentora')

@section('content')
  <section data-component="PageHeader" class="flex flex-col gap-2">
    <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Roles & Permisos</h1>
    <p class="text-muted">Administra roles, permisos y sus relaciones.</p>
  </section>

  <section class="grid grid-cols-1 xl:grid-cols-2 gap-4">
    <article class="card rounded-2xl p-4" id="roles-card">
      <header class="flex items-center justify-between">
        <h3 class="font-semibold">Roles</h3>
        <div class="flex gap-2">
          <input id="qRoles" type="search" placeholder="Buscar rol..." class="px-3 py-2 rounded-lg bg-brand-50 dark:bg-white/5 text-sm outline-none" />
          <button id="btnNewRole" class="px-3 py-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm">Nuevo rol</button>
        </div>
      </header>
      <div id="formRole" class="mt-3 hidden">
        <div class="flex gap-2">
          <input id="roleName" type="text" placeholder="Nombre del rol" class="flex-1 px-3 py-2 rounded-lg bg-brand-50 dark:bg-white/5 text-sm outline-none" />
          <button id="btnSaveRole" class="px-3 py-2 rounded-lg bg-accent text-white text-sm">Guardar</button>
          <button id="btnCancelRole" class="px-3 py-2 rounded-lg bg-white dark:bg-white/10 text-sm">Cancelar</button>
        </div>
      </div>
      <ul id="listRoles" class="mt-3 divide-y divide-white/10"></ul>
      <footer class="mt-3 flex items-center justify-between text-sm text-muted">
        <button id="rolesPrev" class="px-2 py-1 rounded hover:bg-brand-100 dark:hover:bg-white/5">Anterior</button>
        <span id="rolesMeta"></span>
        <button id="rolesNext" class="px-2 py-1 rounded hover:bg-brand-100 dark:hover:bg-white/5">Siguiente</button>
      </footer>
    </article>

    <article class="card rounded-2xl p-4" id="perms-card">
      <header class="flex items-center justify-between">
        <h3 class="font-semibold">Permisos</h3>
        <div class="flex gap-2">
          <input id="qPerms" type="search" placeholder="Buscar permiso..." class="px-3 py-2 rounded-lg bg-brand-50 dark:bg-white/5 text-sm outline-none" />
          <button id="btnNewPerm" class="px-3 py-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm">Nuevo permiso</button>
        </div>
      </header>
      <div id="formPerm" class="mt-3 hidden">
        <div class="flex gap-2">
          <input id="permName" type="text" placeholder="Nombre del permiso" class="flex-1 px-3 py-2 rounded-lg bg-brand-50 dark:bg-white/5 text-sm outline-none" />
          <button id="btnSavePerm" class="px-3 py-2 rounded-lg bg-accent text-white text-sm">Guardar</button>
          <button id="btnCancelPerm" class="px-3 py-2 rounded-lg bg-white dark:bg-white/10 text-sm">Cancelar</button>
        </div>
      </div>
      <ul id="listPerms" class="mt-3 divide-y divide-white/10"></ul>
      <footer class="mt-3 flex items-center justify-between text-sm text-muted">
        <button id="permsPrev" class="px-2 py-1 rounded hover:bg-brand-100 dark:hover:bg-white/5">Anterior</button>
        <span id="permsMeta"></span>
        <button id="permsNext" class="px-2 py-1 rounded hover:bg-brand-100 dark:hover:bg-white/5">Siguiente</button>
      </footer>
    </article>
  </section>

  <section class="grid grid-cols-1 gap-4 mt-4">
    <article class="card rounded-2xl p-4" id="role-perms-card">
      <header class="flex items-center justify-between">
        <h3 class="font-semibold">Permisos del rol</h3>
        <span id="selectedRoleLabel" class="text-sm text-muted">Ningún rol seleccionado</span>
      </header>
      <div class="mt-3">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
          <div>
            <h4 class="text-sm font-medium mb-2">Todos los permisos</h4>
            <div id="allPerms" class="max-h-64 overflow-y-auto rounded-xl border border-white/10 p-3"></div>
          </div>
          <div>
            <h4 class="text-sm font-medium mb-2">Asignados al rol</h4>
            <div id="rolePerms" class="max-h-64 overflow-y-auto rounded-xl border border-white/10 p-3"></div>
          </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-2">
          <button id="btnAttach" class="px-3 py-2 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm">Asignar</button>
          <button id="btnDetach" class="px-3 py-2 rounded-lg bg-white dark:bg-white/10 text-sm">Quitar</button>
          <button id="btnSync" class="px-3 py-2 rounded-lg bg-accent text-white text-sm">Sincronizar</button>
        </div>
      </div>
    </article>
  </section>

  <div id="toast" class="fixed bottom-4 right-4 hidden px-3 py-2 rounded-lg text-sm text-white bg-black/70"></div>
@endsection

@push('scripts')
<script>
(function () {
  const $ = (s, el=document) => el.querySelector(s);
  const $$ = (s, el=document) => Array.from(el.querySelectorAll(s));
  const apiBase = '/api/rbac';
  const state = {
    roles: [],
    rolesMeta: {current_page:1, last_page:1, per_page:10, total:0},
    rolePage: 1,
    roleQ: '',
    editingRoleId: null,
    selectedRole: null,
    rolePerms: [],
    perms: [],
    allPerms: [],
    permsMeta: {current_page:1, last_page:1, per_page:10, total:0},
    permPage: 1,
    permQ: '',
    editingPermId: null,
  };

  const toast = (msg) => {
    const t = $('#toast');
    t.textContent = msg;
    t.classList.remove('hidden');
   setTimeout(()=> t.classList.add('hidden'), 2200);
  };

  const api = async (path, opts={}) => {
    const token = document.querySelector('meta[name="api-token"]')?.getAttribute('content');
    const headers = {
      'Content-Type':'application/json',
      'Accept':'application/json',
      ...(token && {'Authorization': `Bearer ${token}`})
    };
    const res = await fetch(`${apiBase}${path}`, {
      headers,
      ...opts
    });
    if (!res.ok) {
      let err = 'Error';
      try { const js = await res.json(); err = js?.meta?.message || JSON.stringify(js); } catch {}
      throw new Error(err);
    }
    return res.status === 204 ? null : res.json();
  };

  // Render helpers
  const renderRoles = () => {
    const ul = $('#listRoles'); ul.innerHTML = '';
    state.roles.forEach(r => {
      const li = document.createElement('li');
      li.className = 'p-3 flex items-center justify-between hover:bg-brand-100 dark:hover:bg-white/5 rounded-lg';
      li.innerHTML = `
        <button class="text-left flex-1" data-action="select">
          <p class="font-medium">${r.name}</p>
          <p class="text-xs text-muted">#${r.id} · ${r.guard_name}</p>
        </button>
        <div class="flex gap-2">
          <button class="text-sm px-2 py-1 rounded hover:bg-white/50 dark:hover:bg-white/10" data-action="edit">Editar</button>
          <button class="text-sm px-2 py-1 rounded hover:bg-white/50 dark:hover:bg-white/10" data-action="del">Eliminar</button>
        </div>`;
      li.addEventListener('click', (e) => {
        const target = e.target.closest('[data-action]');
        const act = target?.dataset?.action;
        if (act === 'select') selectRole(r);
        if (act === 'edit') { showRoleForm(r); }
        if (act === 'del') { deleteRole(r.id); }
      });
      ul.appendChild(li);
    });
    $('#rolesMeta').textContent = `Página ${state.rolesMeta.current_page} de ${state.rolesMeta.last_page} · ${state.rolesMeta.total} items`;
  };

  const renderPerms = () => {
    const ul = $('#listPerms'); ul.innerHTML = '';
    state.perms.forEach(p => {
      const li = document.createElement('li');
      li.className = 'p-3 flex items-center justify-between hover:bg-brand-100 dark:hover:bg-white/5 rounded-lg';
      li.innerHTML = `
        <div class="text-left">
          <p class="font-medium">${p.name}</p>
          <p class="text-xs text-muted">#${p.id} · ${p.guard_name}</p>
        </div>
        <div class="flex gap-2">
          <button class="text-sm px-2 py-1 rounded hover:bg-white/50 dark:hover:bg-white/10" data-action="edit">Editar</button>
          <button class="text-sm px-2 py-1 rounded hover:bg-white/50 dark:hover:bg-white/10" data-action="del">Eliminar</button>
        </div>`;
      li.addEventListener('click', (e) => {
        const target = e.target.closest('[data-action]');
        const act = target?.dataset?.action;
        if (act === 'edit') { showPermForm(p); }
        if (act === 'del') { deletePermission(p.id); }
      });
      ul.appendChild(li);
    });
    $('#permsMeta').textContent = `Página ${state.permsMeta.current_page} de ${state.permsMeta.last_page} · ${state.permsMeta.total} items`;
  };

  const renderAllPermsCheckboxes = () => {
    const box = $('#allPerms'); box.innerHTML = '';
    const assigned = new Set(state.rolePerms.map(p => p.id));
    state.allPerms.forEach(p => {
      const wrap = document.createElement('label');
      wrap.className = 'flex items-center gap-2 py-1';
      const checkedAttr = assigned.has(p.id) ? 'checked' : '';
      wrap.innerHTML = `<input type="checkbox" value="${p.id}" class="perm-check accent-brand-600" ${checkedAttr}> <span>${p.name}</span>`;
      box.appendChild(wrap);
    });
  };

  const renderRolePerms = () => {
    const box = $('#rolePerms'); box.innerHTML = '';
    state.rolePerms.forEach(p => {
      const wrap = document.createElement('label');
      wrap.className = 'flex items-center gap-2 py-1';
      wrap.innerHTML = `<input type="checkbox" value="${p.id}" class="roleperm-check accent-brand-600" checked> <span>${p.name}</span>`;
      box.appendChild(wrap);
    });
  };

  // Fetchers
  const loadRoles = async () => {
    const q = new URLSearchParams({page: state.rolePage, per_page: 10, sort: 'name', order: 'asc', q: state.roleQ});
    const js = await api(`/roles?${q.toString()}`);
    state.roles = js.data || [];
    state.rolesMeta = js.meta?.pagination || state.rolesMeta;
    renderRoles();
  };

  const loadPermissions = async () => {
    const q = new URLSearchParams({page: state.permPage, per_page: 10, sort: 'name', order: 'asc', q: state.permQ});
    const js = await api(`/permissions?${q.toString()}`);
    state.perms = js.data || [];
    state.permsMeta = js.meta?.pagination || state.permsMeta;
    renderPerms();
  };

  const loadAllPermissions = async () => {
    const perPage = 100;
    let page = 1;
    let all = [];
    while (true) {
      const q = new URLSearchParams({page, per_page: perPage, sort: 'name', order: 'asc'});
      const js = await api(`/permissions?${q.toString()}`);
      const data = js.data || [];
      all = all.concat(data);
      const meta = js.meta?.pagination;
      if (!meta || page >= meta.last_page) break;
      page++;
    }
    state.allPerms = all;
    renderAllPermsCheckboxes();
  };

  const loadRolePermissions = async (roleId) => {
    const js = await api(`/roles/${roleId}/permissions`);
    state.rolePerms = js.data || [];
    renderRolePerms();
    renderAllPermsCheckboxes();
  };

  // Actions Roles
  const showRoleForm = (r=null) => {
    $('#formRole').classList.remove('hidden');
    $('#roleName').value = r?.name || '';
    state.editingRoleId = r?.id || null;
  };
  const hideRoleForm = () => {
    $('#formRole').classList.add('hidden');
    $('#roleName').value = '';
    state.editingRoleId = null;
  };
  const saveRole = async () => {
    const name = $('#roleName').value.trim();
    if (!name) return toast('Nombre requerido');
    try {
      if (state.editingRoleId) {
        await api(`/roles/${state.editingRoleId}`, {method:'PUT', body: JSON.stringify({name, guard_name:'web'})});
        toast('Rol actualizado');
      } else {
        await api('/roles', {method:'POST', body: JSON.stringify({name, guard_name:'web'})});
        toast('Rol creado');
      }
      hideRoleForm(); await loadRoles();
    } catch (e) { toast(e.message); }
  };
  const deleteRole = async (id) => {
    if (!confirm('¿Eliminar rol?')) return;
    try { await api(`/roles/${id}`, {method:'DELETE'}); toast('Rol eliminado'); await loadRoles(); } catch(e){ toast(e.message); }
  };
  const selectRole = async (r) => {
    state.selectedRole = r;
    $('#selectedRoleLabel').textContent = `Rol seleccionado: ${r.name} (#${r.id})`;
    await loadRolePermissions(r.id);
  };

  // Actions Permisos
  const showPermForm = (p=null) => {
    $('#formPerm').classList.remove('hidden');
    $('#permName').value = p?.name || '';
    state.editingPermId = p?.id || null;
  };
  const hidePermForm = () => {
    $('#formPerm').classList.add('hidden');
    $('#permName').value = '';
    state.editingPermId = null;
  };
  const savePerm = async () => {
    const name = $('#permName').value.trim();
    if (!name) return toast('Nombre requerido');
    try {
      if (state.editingPermId) {
        await api(`/permissions/${state.editingPermId}`, {method:'PUT', body: JSON.stringify({name, guard_name:'web'})});
        toast('Permiso actualizado');
      } else {
        await api('/permissions', {method:'POST', body: JSON.stringify({name, guard_name:'web'})});
        toast('Permiso creado');
      }
      hidePermForm(); await loadPermissions(); await loadAllPermissions();
    } catch (e) { toast(e.message); }
  };
  const deletePermission = async (id) => {
    if (!confirm('¿Eliminar permiso?')) return;
    try { await api(`/permissions/${id}`, {method:'DELETE'}); toast('Permiso eliminado'); await loadPermissions(); await loadAllPermissions(); } catch(e){ toast(e.message); }
  };

  // Role-Permission ops
  const getChecked = (cls) => $$('.' + cls).filter(i => i.checked).map(i => Number(i.value));
  const attach = async () => {
    if (!state.selectedRole) return toast('Selecciona un rol');
    const ids = getChecked('perm-check');
    if (!ids.length) return toast('Selecciona permisos');
    try {
      await api(`/roles/${state.selectedRole.id}/permissions/attach`, {method:'POST', body: JSON.stringify({permissions: ids, mode:'by_id', guard_name:'web'})});
      toast('Permisos asignados');
      await loadRolePermissions(state.selectedRole.id);
    } catch(e){ toast(e.message); }
  };
  const detach = async () => {
    if (!state.selectedRole) return toast('Selecciona un rol');
    const ids = getChecked('roleperm-check');
    if (!ids.length) return toast('Selecciona permisos asignados');
    try {
      await api(`/roles/${state.selectedRole.id}/permissions/detach`, {method:'POST', body: JSON.stringify({permissions: ids, mode:'by_id', guard_name:'web'})});
      toast('Permisos quitados');
      await loadRolePermissions(state.selectedRole.id);
    } catch(e){ toast(e.message); }
  };
  const sync = async () => {
    if (!state.selectedRole) return toast('Selecciona un rol');
    const ids = getChecked('perm-check');
    try {
      await api(`/roles/${state.selectedRole.id}/permissions/sync`, {method:'POST', body: JSON.stringify({permissions: ids, mode:'by_id', guard_name:'web'})});
      toast('Permisos sincronizados');
      await loadRolePermissions(state.selectedRole.id);
    } catch(e){ toast(e.message); }
  };

  // Bindings
  $('#btnNewRole').addEventListener('click', () => showRoleForm());
  $('#btnCancelRole').addEventListener('click', hideRoleForm);
  $('#btnSaveRole').addEventListener('click', saveRole);
  $('#qRoles').addEventListener('input', (e)=> { state.roleQ = e.target.value; state.rolePage=1; debounceLoadRoles(); });
  $('#rolesPrev').addEventListener('click', ()=> { if (state.rolesMeta.current_page>1){ state.rolePage--; loadRoles(); } });
  $('#rolesNext').addEventListener('click', ()=> { if (state.rolesMeta.current_page<state.rolesMeta.last_page){ state.rolePage++; loadRoles(); } });

  $('#btnNewPerm').addEventListener('click', () => showPermForm());
  $('#btnCancelPerm').addEventListener('click', hidePermForm);
  $('#btnSavePerm').addEventListener('click', savePerm);
  $('#qPerms').addEventListener('input', (e)=> { state.permQ = e.target.value; state.permPage=1; debounceLoadPerms(); });
  $('#permsPrev').addEventListener('click', ()=> { if (state.permsMeta.current_page>1){ state.permPage--; loadPermissions(); } });
  $('#permsNext').addEventListener('click', ()=> { if (state.permsMeta.current_page<state.permsMeta.last_page){ state.permPage++; loadPermissions(); } });

  $('#btnAttach').addEventListener('click', attach);
  $('#btnDetach').addEventListener('click', detach);
  $('#btnSync').addEventListener('click', sync);

  // Debouncers
  let rt=null, pt=null;
  const debounceLoadRoles = () => { clearTimeout(rt); rt=setTimeout(()=>{ loadRoles(); }, 350); };
  const debounceLoadPerms = () => { clearTimeout(pt); pt=setTimeout(()=>{ loadPermissions(); }, 350); };

  // Init
  loadRoles();
  loadPermissions();
  loadAllPermissions();
})();
</script>
@endpush