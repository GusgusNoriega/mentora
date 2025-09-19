@extends('dashboard')
@section('title', 'Administrador de Archivos')

@section('content')
<div class="grid gap-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Administrador de archivos</h1>
    <div class="flex items-center gap-2">
      <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-mm="refresh">Actualizar</button>
      <button class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow" data-mm="open-upload">Subir archivo</button>
      <button class="px-3 py-2 rounded-lg bg-accent text-white hover:opacity-90 shadow" data-mm="open-url">Agregar URL / Video</button>
    </div>
  </div>

  {{-- Toolbar de filtros/búsqueda --}}
  <div class="rounded-2xl border border-slate-200/60 dark:border-slate-700/60 p-4 bg-white dark:bg-slate-900">
    <div class="grid gap-3 md:grid-cols-3">
      <div class="flex gap-2">
        <input type="text" data-mm="search" placeholder="Buscar por nombre, URL, MIME…" class="min-w-[160px] flex-1 rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" aria-label="Buscar">
        <select data-mm="type" class="rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" aria-label="Filtrar por tipo">
          <option value="">Todos</option>
          <option value="image">Imágenes</option>
          <option value="video">Videos</option>
          <option value="audio">Audios</option>
          <option value="document">Documentos</option>
        </select>
      </div>
      <div class="flex items-center gap-2 text-sm text-slate-500">
        <span>Página:</span>
        <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 disabled:opacity-50" data-mm="prev">Anterior</button>
        <div class="text-sm text-slate-500" data-mm="pageinfo">—</div>
        <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 disabled:opacity-50" data-mm="next">Siguiente</button>
      </div>
      <div class="flex items-center justify-end gap-2" data-mm="selection-bar" hidden>
        <div class="text-sm text-slate-600 dark:text-slate-300" data-mm="selection">0 seleccionados</div>
        <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-mm="picked-clear">Quitar todo</button>
        <button class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700" data-mm="bulk-delete">Eliminar seleccionados</button>
      </div>
    </div>
  </div>

  {{-- Grilla principal --}}
  <div class="rounded-2xl border border-slate-200/60 dark:border-slate-700/60 p-3 bg-white dark:bg-slate-900">
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3" data-mm="grid" role="list"></div>
  </div>
</div>

{{-- Panel Editor --}}
<div id="media_manager-editor" class="fixed right-0 top-0 h-full w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200/60 dark:border-slate-700/60 translate-x-full transition-transform duration-200 z-[75] flex flex-col">
  <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200/60 dark:border-slate-700/60">
    <div class="font-semibold">Editar medio</div>
    <button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" data-ed="close" aria-label="Cerrar">✕</button>
  </div>
  <div class="flex-1 overflow-y-auto p-4 space-y-4">
    <div class="rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60" data-ed="thumb"></div>
    <div class="grid gap-3">
      <div>
        <label class="text-sm text-slate-500">ID</label>
        <input type="text" class="w-full rounded-lg border px-3 py-2 bg-slate-50 dark:bg-slate-800" data-ed="id" readonly>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div>
          <label class="text-sm text-slate-500">Tipo</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2 bg-slate-50 dark:bg-slate-800" data-ed="type" readonly>
        </div>
        <div>
          <label class="text-sm text-slate-500">Proveedor</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2 bg-slate-50 dark:bg-slate-800" data-ed="provider" readonly>
        </div>
      </div>
      <div>
        <label class="text-sm text-slate-500">Nombre</label>
        <input type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ed="name" placeholder="Nombre legible">
      </div>
      <div>
        <label class="text-sm text-slate-500">Texto alternativo (alt)</label>
        <input type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ed="alt" placeholder="Descripción corta para accesibilidad">
      </div>
      <div data-ed="url-wrap" class="hidden">
        <label class="text-sm text-slate-500">URL (solo para videos embebidos)</label>
        <input type="url" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ed="url" placeholder="https://…">
        <p class="text-xs text-slate-500 mt-1">Solo modificable cuando el tipo es <strong>video</strong>.</p>
      </div>
    </div>
    <div class="flex items-center justify-between pt-2">
      <button class="px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700" data-ed="delete">Eliminar</button>
      <div class="flex gap-2">
        <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-ed="cancel">Cancelar</button>
        <button class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700" data-ed="save">Guardar</button>
      </div>
    </div>
  </div>
</div>

{{-- Toast --}}
<div id="media_manager-toast" class="fixed left-1/2 -translate-x-1/2 bottom-6 px-4 py-2 rounded-xl text-white bg-slate-900/90 hidden" role="status" aria-live="polite"></div>

{{-- MODAL: Subir archivo --}}
<div id="media_manager-upload" class="fixed inset-0 z-[10000] hidden">
  <div class="fixed inset-0 bg-black/45 backdrop-blur-sm" data-up="backdrop"></div>
  <div class="relative mx-auto my-6 w-[95vw] max-w-lg">
    <div class="transform transition duration-200 ease-out translate-y-2 opacity-0 rounded-2xl border border-white/10 bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl shadow-2xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200/60 dark:border-slate-700/60">
        <div class="font-semibold">Subir archivo</div>
        <button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" data-up="close" aria-label="Cerrar">✕</button>
      </div>
      <div class="p-4 grid gap-3">
        <div>
          <label class="text-sm text-slate-500">Archivo</label>
          <input type="file" class="block w-full text-sm" data-up="file">
        </div>
        <div>
          <label class="text-sm text-slate-500">Nombre (opcional)</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-up="name" placeholder="Nombre legible">
        </div>
        <div>
          <label class="text-sm text-slate-500">Alt (opcional)</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-up="alt" placeholder="Texto alternativo">
        </div>
      </div>
      <div class="px-4 py-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-end gap-2">
        <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-up="cancel">Cancelar</button>
        <button class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700" data-up="save">Subir</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL: Agregar URL / Video --}}
<div id="media_manager-url" class="fixed inset-0 z-[10000] hidden">
  <div class="fixed inset-0 bg-black/45 backdrop-blur-sm" data-ur="backdrop"></div>
  <div class="relative mx-auto my-6 w-[95vw] max-w-lg">
    <div class="transform transition duration-200 ease-out translate-y-2 opacity-0 rounded-2xl border border-white/10 bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl shadow-2xl overflow-hidden">
      <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200/60 dark:border-slate-700/60">
        <div class="font-semibold">Agregar URL / Video</div>
        <button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" data-ur="close" aria-label="Cerrar">✕</button>
      </div>
      <div class="p-4 grid gap-3">
        <div>
          <label class="text-sm text-slate-500">URL</label>
          <input type="url" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ur="url" placeholder="https://vimeo.com/…">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="text-sm text-slate-500">Tipo</label>
            <select class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ur="type">
              <option value="video" selected>video</option>
              <option value="image">image</option>
              <option value="audio">audio</option>
              <option value="document">document</option>
            </select>
          </div>
          <div>
            <label class="text-sm text-slate-500">Proveedor</label>
            <input type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ur="provider" value="vimeo" placeholder="vimeo / youtube / external">
          </div>
        </div>
        <div>
          <label class="text-sm text-slate-500">Nombre (opcional)</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ur="name" placeholder="Nombre legible">
        </div>
        <div>
          <label class="text-sm text-slate-500">Alt (opcional)</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ur="alt" placeholder="Texto alternativo">
        </div>
      </div>
      <div class="px-4 py-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-end gap-2">
        <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-ur="cancel">Cancelar</button>
        <button class="px-3 py-2 rounded-lg bg-accent text-white hover:opacity-90" data-ur="save">Agregar</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
  // ===== Utils =====
  const $ = (sel, el = document) => el.querySelector(sel);
  const $$ = (sel, el = document) => Array.from(el.querySelectorAll(sel));

  const token = document.querySelector('meta[name="api-token"]')?.getAttribute('content') || '';
  const csrf  = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const API   = '/mentora/public/api/media'; // 👈 mismas APIs

  const headers = () => {
    const h = { Accept: 'application/json' };
    if (token) h['Authorization'] = `Bearer ${token}`;
    if (csrf)  h['X-CSRF-TOKEN'] = csrf;
    return h;
  };

  const formatBytes = (b) => {
    if (b == null) return '';
    const u=['B','KB','MB','GB']; let i=0, n=b;
    while (n>=1024 && i<u.length-1) { n/=1024; i++; }
    return `${n.toFixed(1)} ${u[i]}`;
  };

  const toast = (msg, ms=2200) => {
    const t = $('#media_manager-toast');
    if (!t) return;
    t.textContent = msg; t.classList.remove('hidden');
    clearTimeout(t._h); t._h = setTimeout(() => t.classList.add('hidden'), ms);
  };

  const openModal  = (id) => { const m=$(id); if(!m) return; m.classList.remove('hidden'); const c=m.querySelector('.transform'); if (c){ requestAnimationFrame(()=>{ c.classList.remove('translate-y-2','opacity-0'); }); } document.documentElement.style.overflow='hidden'; document.body.style.overflow='hidden'; };
  const closeModal = (id) => { const m=$(id); if(!m) return; m.classList.add('hidden'); document.documentElement.style.overflow=''; document.body.style.overflow=''; };

  // ====== MediaManager (SIN inputs) ======
  const state = {
    q: '',
    type: '',
    page: 1,
    perPage: 24,
    selected: new Set(), // para acciones masivas
    current: null, // item abierto en editor
  };

  // DOM refs
  const grid = document.querySelector('[data-mm="grid"]');
  const pageInfo = document.querySelector('[data-mm="pageinfo"]');
  const selectionBar = document.querySelector('[data-mm="selection-bar"]');
  const selectionInfo = document.querySelector('[data-mm="selection"]');

  const setSelectionUI = () => {
    const c = state.selected.size;
    selectionInfo.textContent = `${c} seleccionado${c===1?'':'s'}`;
    selectionBar.hidden = c === 0;
  };

  const renderSkeleton = (n=12) => Array.from({length:n}).map(()=>`
    <div class="rounded-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden animate-pulse">
      <div class="aspect-square bg-slate-200/60 dark:bg-slate-700/60"></div>
      <div class="p-2 space-y-2">
        <div class="h-3 rounded bg-slate-200/80 dark:bg-slate-700/80"></div>
        <div class="h-3 w-1/2 rounded bg-slate-200/70 dark:bg-slate-700/70"></div>
      </div>
    </div>
  `).join('');

  const renderEmpty = () => `
    <div class="col-span-full py-14 text-center">
      <div class="text-5xl mb-2">🗂️</div>
      <div class="text-base font-medium">No hay medios aún</div>
      <div class="text-sm text-slate-500">Sube un archivo o agrega una URL externa</div>
    </div>`;

  const thumbFor = (it, fit='cover') => {
    const url = it.url || '';
    const t = it.type;
    if (t === 'image'){
      if (fit === 'contain') return `<img src="${url}" alt="" class="max-h-full max-w-full object-contain">`;
      return `<img src="${url}" alt="" class="w-full h-full object-cover">`;
    }
    if (t === 'video') return `<div class="text-center text-xs"><div class="text-2xl">🎬</div><div class="text-slate-500">${it.provider||'video'}</div></div>`;
    if (t === 'audio') return `<div class="text-2xl">🎵</div>`;
    return `<div class="text-2xl">📄</div>`;
  };

  const renderItem = (it) => {
    const size = formatBytes(it.size_bytes);
    const id = it.id;
    return `
    <div class="group relative" role="listitem">
      <div class="absolute top-2 left-2 z-10">
        <input type="checkbox" class="h-4 w-4" data-mm="pick" data-id="${id}">
      </div>
      <button type="button" class="w-full text-left rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60 hover:shadow transition ring-offset-2" data-mm-item data-id="${id}" aria-label="Abrir editor">
        <div class="aspect-square bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
          ${thumbFor(it,'cover')}
        </div>
        <div class="p-2">
          <div class="text-xs font-medium line-clamp-1">#${id} • ${it.type||''}</div>
          ${it.name ? `<div class="text-[11px] text-slate-700 dark:text-slate-300 line-clamp-1" title="${it.name}">${it.name}</div>` : ''}
          <div class="text-[11px] text-slate-500">${size||''}</div>
        </div>
      </button>
      <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
        <button type="button" class="px-2 py-1 rounded-md bg-white/90 dark:bg-slate-900/80 border border-slate-200/60 dark:border-slate-700/60 shadow" data-mm-edit="${id}" aria-label="Editar">⋯</button>
      </div>
    </div>`;
  };

  const load = async () => {
    if(!grid) return;
    grid.innerHTML = renderSkeleton(state.perPage);
    try{
      const p=new URLSearchParams({ page:String(state.page), per_page:String(state.perPage) });
      if(state.q)   p.append('q',state.q);
      if(state.type)p.append('type',state.type);

      const res=await fetch(`${API}?${p.toString()}`,{ headers:headers() });
      if(!res.ok) throw new Error('Error al listar medios');
      const data=await res.json();

      pageInfo.textContent=`Página ${data.current_page} de ${data.last_page||1}`;
      document.querySelector('[data-mm="prev"]').disabled = data.current_page <= 1;
      document.querySelector('[data-mm="next"]').disabled = data.current_page >= (data.last_page || 1);

      const items=data.data||[];
      if (!items.length){ grid.innerHTML=renderEmpty(); return; }
      grid.innerHTML = items.map(renderItem).join('');

      // restaurar checks
      $$('[data-mm="pick"]', grid).forEach(cb=>{
        const id = cb.getAttribute('data-id');
        cb.checked = state.selected.has(id);
        cb.addEventListener('change', ()=>{
          if (cb.checked) state.selected.add(id); else state.selected.delete(id);
          setSelectionUI();
        });
      });

      // abrir editor con click
      $$('[data-mm-item]', grid).forEach(btn=>{
        btn.addEventListener('click', ()=> openEditor(btn.getAttribute('data-id')));
      });
      $$('[data-mm-edit]', grid).forEach(btn=>{
        btn.addEventListener('click', (e)=>{ e.stopPropagation(); openEditor(btn.getAttribute('data-mm-edit')); });
      });
    }catch(e){
      console.error(e);
      grid.innerHTML = `<div class="col-span-full text-center text-sm text-red-500">No se pudieron cargar los medios.</div>`;
    }
  };

  // === Editor ===
  const ed = $('#media_manager-editor');
  const toggleVideoUrlVisibility = (isVideo) => {
    const wrap=$('[data-ed="url-wrap"]', ed); if(!wrap) return; wrap.classList.toggle('hidden', !isVideo);
  };

  const openEditor = async (id) => {
    ed.dataset.currentId=id;
    try{
      const res=await fetch(`${API}/${id}`,{headers:headers()});
      if(!res.ok) throw new Error('No se pudo cargar');
      const it=await res.json();
      $('[data-ed="id"]',ed).value=it.id;
      $('[data-ed="type"]',ed).value=it.type || 'document';
      $('[data-ed="provider"]',ed).value=it.provider || '';
      $('[data-ed="name"]',ed).value=it.name || '';
      $('[data-ed="alt"]',ed).value=it.alt || '';
      $('[data-ed="url"]',ed).value=it.url || '';
      toggleVideoUrlVisibility((it.type||'')==='video');
      $('[data-ed="thumb"]',ed).innerHTML = `
        <div class="aspect-video bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
          ${thumbFor(it,'cover')}
        </div>
        <div class="p-2 text-xs text-slate-500">#${it.id} • ${it.mime_type||''} • ${formatBytes(it.size_bytes)||''}</div>
      `;
      ed.classList.remove('translate-x-full');
    }catch(e){ console.error(e); toast('No se pudo abrir el editor',2400); }
  };
  const closeEditor = ()=> ed.classList.add('translate-x-full');

  const saveEditor = async () => {
    const id=ed.dataset.currentId;
    const type=$('[data-ed="type"]',ed).value;
    const name=$('[data-ed="name"]',ed).value.trim();
    const alt =$('[data-ed="alt"]',ed).value.trim();
    const url =$('[data-ed="url"]',ed).value.trim();
    const payload={};
    if(name!=='') payload.name=name;
    if(alt!=='')  payload.alt =alt;
    if(type==='video' && url!=='') payload.url=url;
    if(!Object.keys(payload).length){ toast('Nada para guardar'); return; }
    try{
      const res=await fetch(`${API}/${id}`,{method:'PATCH',headers:{...headers(),'Content-Type':'application/json'},body:JSON.stringify(payload)});
      if(!res.ok) throw new Error('Error al guardar');
      await res.json(); toast('Cambios guardados'); closeEditor(); load();
    }catch(e){ console.error(e); toast('No se pudieron guardar los cambios',2600); }
  };

  const deleteSingle = async () => {
    const id=ed.dataset.currentId; if(!id) return;
    if(!confirm('¿Eliminar este archivo? Esta acción no se puede deshacer.')) return;
    try{
      const res=await fetch(`${API}/${id}`,{method:'DELETE',headers:headers()});
      if(!res.ok) throw new Error('Error al eliminar');
      toast('Eliminado'); closeEditor(); state.selected.delete(String(id)); setSelectionUI(); load();
    }catch(e){ console.error(e); toast('No se pudo eliminar',2600); }
  };

  // === Acciones masivas ===
  const bulkDelete = async () => {
    const ids = Array.from(state.selected);
    if(!ids.length) return;
    if(!confirm(`¿Eliminar ${ids.length} archivo${ids.length>1?'s':''}?`)) return;
    try{
      // Si tu API soporta DELETE masivo, ajústalo aquí. Por defecto, iteramos.
      for (const id of ids){
        const res=await fetch(`${API}/${id}`,{method:'DELETE',headers:headers()});
        if(!res.ok) throw new Error('Error al eliminar #'+id);
      }
      toast('Eliminación completa');
      state.selected.clear(); setSelectionUI(); load();
    }catch(e){ console.error(e); toast('Ocurrió un error durante la eliminación',2600); }
  };

  // === Subir / Agregar URL ===
  const uploadModal = '#media_manager-upload';
  const urlModal = '#media_manager-url';

  const uploadFromModal = async () => {
    const up=$(uploadModal);
    const file=$('[data-up="file"]',up)?.files?.[0];
    const name=$('[data-up="name"]',up)?.value?.trim();
    const alt =$('[data-up="alt"]',up)?.value?.trim();
    if (!file){ toast('Selecciona un archivo'); return; }
    const fd=new FormData(); fd.append('file',file); if(name) fd.append('name',name); if(alt) fd.append('alt',alt);
    try{
      const res=await fetch(API,{method:'POST',headers:headers(),body:fd});
      if(!res.ok) throw new Error('Error al subir archivo');
      await res.json(); toast('Archivo subido');
      $('[data-up="file"]',up).value=''; $('[data-up="name"]',up).value=''; $('[data-up="alt"]',up).value='';
      closeModal(uploadModal); state.page=1; load();
    }catch(e){ console.error(e); toast('No se pudo subir',2600); }
  };

  const addUrlFromModal = async () => {
    const um=$(urlModal);
    const url=$('[data-ur="url"]',um)?.value?.trim();
    const type=$('[data-ur="type"]',um)?.value || 'video';
    const provider=($('[data-ur="provider"]',um)?.value || 'external').trim() || 'external';
    const name=$('[data-ur="name"]',um)?.value?.trim();
    const alt =$('[data-ur="alt"]',um)?.value?.trim();
    if(!url) return toast('Ingresa una URL válida');
    try{
      const body={url,type,provider}; if(name) body.name=name; if(alt) body.alt=alt;
      const res=await fetch(API,{method:'POST',headers:{...headers(),'Content-Type':'application/json'},body:JSON.stringify(body)});
      if(!res.ok) throw new Error('Error al agregar URL');
      await res.json();
      $('[data-ur="url"]',um).value=''; $('[data-ur="name"]',um).value=''; $('[data-ur="alt"]',um).value='';
      closeModal(urlModal); toast('URL agregada'); state.page=1; load();
    }catch(e){ console.error(e); toast('No se pudo agregar URL',2600); }
  };

  // === Bindings ===
  document.querySelector('[data-mm="search"]').addEventListener('input', (e)=>{ state.q=e.target.value; state.page=1; clearTimeout(state._t); state._t=setTimeout(load, 300); });
  document.querySelector('[data-mm="type"]').addEventListener('change', (e)=>{ state.type=e.target.value; state.page=1; load(); });
  document.querySelector('[data-mm="prev"]').addEventListener('click', ()=>{ if(state.page>1){ state.page--; load(); } });
  document.querySelector('[data-mm="next"]').addEventListener('click', ()=>{ state.page++; load(); });
  document.querySelector('[data-mm="refresh"]').addEventListener('click', ()=> load());

  document.querySelector('[data-mm="picked-clear"]').addEventListener('click', ()=>{ state.selected.clear(); setSelectionUI(); $$('[data-mm="pick"]',grid).forEach(cb=>cb.checked=false); });
  document.querySelector('[data-mm="bulk-delete"]').addEventListener('click', bulkDelete);

  document.querySelector('[data-mm="open-upload"]').addEventListener('click', ()=> openModal(uploadModal));
  document.querySelector('[data-mm="open-url"]').addEventListener('click', ()=> openModal(urlModal));

  // Upload modal
  $('[data-up="save"]').addEventListener('click', uploadFromModal);
  ;['cancel','close','backdrop'].forEach(k=>{ $(`[data-up="${k}"]`).addEventListener('click', ()=> closeModal(uploadModal)); });

  // URL modal
  $('[data-ur="save"]').addEventListener('click', addUrlFromModal);
  ;['cancel','close','backdrop'].forEach(k=>{ $(`[data-ur="${k}"]`).addEventListener('click', ()=> closeModal(urlModal)); });

  // Editor buttons
  $('[data-ed="save"]').addEventListener('click', saveEditor);
  $('[data-ed="delete"]').addEventListener('click', deleteSingle);
  ;['close','cancel'].forEach(k=>{ $(`[data-ed="${k}"]`).addEventListener('click', closeEditor); });

  // Escape cierra modal/editor
  document.addEventListener('keydown', (e)=>{
    if(e.key !== 'Escape') return;
    if (!$('#media_manager-upload').classList.contains('hidden')) { closeModal(uploadModal); return; }
    if (!$('#media_manager-url').classList.contains('hidden'))   { closeModal(urlModal);   return; }
    if (!$('#media_manager-editor').classList.contains('translate-x-full')) { closeEditor(); return; }
  });

  // Init
  setSelectionUI();
  load();
})();
</script>
@endpush