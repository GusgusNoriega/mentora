@extends('dashboard') {{-- o "dasboard" si tu layout se llama así --}}

@section('title', 'Administrador de Archivos')

@push('head')
<style>
  .line-clamp-1{display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden}
  .mf-scrollbar::-webkit-scrollbar{height:8px;width:8px}
  .mf-scrollbar::-webkit-scrollbar-thumb{background-color:rgba(148,163,184,.5);border-radius:.5rem}
</style>
@endpush

@section('content')
<div class="grid gap-6">
  <h1 class="text-2xl font-semibold">Administrador de archivos</h1>

  {{-- DEMO: selector ÚNICO --}}
  <div class="card rounded-xl2 p-4">
    <h2 class="font-medium mb-3">Selector único</h2>
    <div class="flex items-center gap-3">
      <input id="media_single_input" type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800"
             placeholder="ID del archivo" readonly
             data-filepicker="single"
             data-fp-preview="#single_preview">
      <button type="button"
              class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow"
              data-fp-open="#media_single_input">
        Seleccionar
      </button>
    </div>
    <div id="single_preview" class="mt-3"></div>
  </div>

  {{-- DEMO: selector MÚLTIPLE --}}
  <div class="card rounded-xl2 p-4">
    <h2 class="font-medium mb-3">Selector múltiple</h2>
    <div class="flex items-center gap-3">
      <input id="media_multi_input" type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800"
             placeholder="IDs separados por comas" readonly
             data-filepicker="multiple"
             data-fp-preview="#multi_preview">
      <button type="button"
              class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow"
              data-fp-open="#media_multi_input">
        Seleccionar
      </button>
    </div>
    <div id="multi_preview" class="mt-3 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
  </div>
</div>

{{-- MODAL raíz --}}
<div id="mf-root" class="fixed inset-0 z-[70] hidden">
  <div class="absolute inset-0 bg-black/45 backdrop-blur-sm" data-mf="backdrop"></div>

  <div class="relative mx-auto my-4 w-[98vw] max-w-6xl md:my-10">
    <div class="rounded-2xl border border-white/10 bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl shadow-2xl overflow-hidden">
      {{-- Header --}}
      <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200/60 dark:border-slate-700/60">
        <div class="flex items-center gap-3">
          <div class="text-lg font-semibold">Biblioteca de medios</div>
          <span class="hidden md:inline text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-muted"
                data-mf="instance"></span>
        </div>
        <button type="button" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" data-mf="close" aria-label="Cerrar">✕</button>
      </div>

      {{-- Toolbar (sticky y responsiva) --}}
      <div class="px-4 py-3 border-b border-slate-200/60 dark:border-slate-700/60 sticky top-0 bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl z-10">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3 items-stretch">
          {{-- Grupo búsqueda/filtro --}}
          <div class="flex flex-wrap gap-2">
            <input type="text" data-mf="search" placeholder="Buscar…"
                   class="min-w-[180px] flex-1 rounded-lg border px-3 py-2 bg-white dark:bg-slate-800">
            <select data-mf="type" class="rounded-lg border px-3 py-2 bg-white dark:bg-slate-800">
              <option value="">Todos</option>
              <option value="image">Imágenes</option>
              <option value="video">Videos</option>
              <option value="audio">Audios</option>
              <option value="document">Documentos</option>
            </select>
            <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-mf="refresh">Actualizar</button>
          </div>

          {{-- Grupo subir archivo --}}
          <div class="flex flex-wrap gap-2">
            <label class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow cursor-pointer">
              Subir archivo
              <input type="file" class="hidden" data-mf="upload">
            </label>
            <div class="text-xs text-muted self-center hidden md:block">JPG, PNG, WEBP, PDF, MP4…</div>
          </div>

          {{-- Grupo URL externa --}}
          <div class="flex flex-wrap gap-2">
            <input type="url" data-mf="ext-url" placeholder="URL externa (ej. Vimeo)"
                   class="min-w-[180px] flex-1 rounded-lg border px-3 py-2 bg-white dark:bg-slate-800">
            <select data-mf="ext-type" class="rounded-lg border px-3 py-2 bg-white dark:bg-slate-800">
              <option value="video">video</option>
              <option value="image">image</option>
              <option value="audio">audio</option>
              <option value="document">document</option>
            </select>
            <input type="text" data-mf="ext-provider" placeholder="provider" value="vimeo"
                   class="w-28 rounded-lg border px-3 py-2 bg-white dark:bg-slate-800">
            <button class="px-3 py-2 rounded-lg bg-accent text-white hover:opacity-90 shadow" data-mf="add-url">
              Agregar URL
            </button>
          </div>
        </div>
      </div>

      {{-- Grid --}}
      <div class="p-4">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3 mf-scrollbar" data-mf="grid"></div>

        {{-- Paginación --}}
        <div class="flex justify-between items-center mt-4">
          <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 disabled:opacity-50" data-mf="prev">Anterior</button>
          <div class="text-sm text-muted" data-mf="pageinfo">Página 1</div>
          <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 disabled:opacity-50" data-mf="next">Siguiente</button>
        </div>
      </div>

      {{-- Footer --}}
      <div class="px-4 py-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
        <div class="text-sm text-muted" data-mf="selection">0 seleccionados</div>
        <div class="flex items-center gap-2">
          <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-mf="clear">Limpiar</button>
          <button class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow" data-mf="use">Usar selección</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Panel Editor/Inspector --}}
  <div id="mf-editor" class="fixed right-0 top-0 h-full w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200/60 dark:border-slate-700/60 translate-x-full transition-transform duration-200 z-[75]">
    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200/60 dark:border-slate-700/60">
      <div class="font-semibold">Editar medio</div>
      <button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" data-ed="close" aria-label="Cerrar">✕</button>
    </div>

    <div class="p-4 space-y-4">
      <div class="rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60" data-ed="thumb"></div>

      <div class="grid gap-3">
        <div>
          <label class="text-sm text-muted">ID</label>
          <input type="text" class="w-full rounded-lg border px-3 py-2 bg-slate-50 dark:bg-slate-800" data-ed="id" readonly>
        </div>
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="text-sm text-muted">Tipo</label>
            <select class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ed="type">
              <option value="image">image</option>
              <option value="video">video</option>
              <option value="audio">audio</option>
              <option value="document">document</option>
            </select>
          </div>
          <div>
            <label class="text-sm text-muted">Proveedor</label>
            <input type="text" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ed="provider" placeholder="local / vimeo / youtube / external">
          </div>
        </div>
        <div>
          <label class="text-sm text-muted">URL (solo externos)</label>
          <input type="url" class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" data-ed="url" placeholder="https://…">
          <p class="text-xs text-muted mt-1">Si colocas una URL, el archivo local (si lo hay) se marcará como externo.</p>
        </div>
        <div>
          <label class="text-sm text-muted">Reemplazar archivo (local)</label>
          <input type="file" class="block w-full text-sm" data-ed="file">
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
  <div id="mf-toast" class="fixed left-1/2 -translate-x-1/2 bottom-6 px-4 py-2 rounded-xl text-white bg-slate-900/90 hidden"></div>
</div>

@push('scripts')
<script>
(() => {
  // ===== Utils =====
  const $ = (sel, el=document) => el.querySelector(sel);
  const $$ = (sel, el=document) => Array.from(el.querySelectorAll(sel));
  const token = $('meta[name="api-token"]')?.getAttribute('content') || '';
  const csrf  = $('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const API   = '/mentora/public/api/media';
  const headers = () => {
    const h = {};
    if (token) h['Authorization'] = `Bearer ${token}`;
    if (csrf)  h['X-CSRF-TOKEN'] = csrf;
    return h;
  };
  const uid = (p='mf') => `${p}_${Date.now().toString(36)}_${Math.random().toString(36).slice(2,8)}`;
  const formatBytes = b => { if (b==null) return ''; const u=['B','KB','MB','GB']; let i=0,n=b; while(n>=1024&&i<u.length-1){n/=1024;i++;} return `${n.toFixed(1)} ${u[i]}`; };
  const toast = (msg, ms=2200) => {
    const t = $('#mf-toast'); if(!t) return; t.textContent = msg; t.classList.remove('hidden'); clearTimeout(t._h);
    t._h = setTimeout(()=>t.classList.add('hidden'), ms);
  };

  class MediaFinder {
    constructor({input}) {
      this.instanceId = uid();
      this.input = input;
      this.mode = (input.getAttribute('data-filepicker') || 'single').toLowerCase(); // 'single'|'multiple'
      this.previewSelector = input.getAttribute('data-fp-preview') || '';
      this.previewEl = this.previewSelector ? $(this.previewSelector) : null;

      this.q = ''; this.type = ''; this.page = 1; this.perPage = 18;
      this.selected = new Set();

      this.root = $('#mf-root');
      this.grid = $('[data-mf="grid"]', this.root);
      this.pageInfo = $('[data-mf="pageinfo"]', this.root);
      this.selInfo = $('[data-mf="selection"]', this.root);
      this.btnPrev = $('[data-mf="prev"]', this.root);
      this.btnNext = $('[data-mf="next"]', this.root);
      this.search = $('[data-mf="search"]', this.root);
      this.selType = $('[data-mf="type"]', this.root);
      this.fileInput = $('[data-mf="upload"]', this.root);
      this.btnRefresh = $('[data-mf="refresh"]', this.root);
      this.btnUse = $('[data-mf="use"]', this.root);
      this.btnClear = $('[data-mf="clear"]', this.root);
      this.btnClose = $('[data-mf="close"]', this.root);
      this.backdrop = $('[data-mf="backdrop"]', this.root);
      this.instanceLabel = $('[data-mf="instance"]', this.root);

      this.extUrl = $('[data-mf="ext-url"]', this.root);
      this.extType = $('[data-mf="ext-type"]', this.root);
      this.extProv = $('[data-mf="ext-provider"]', this.root);
      this.btnAddUrl = $('[data-mf="add-url"]', this.root);

      if (this.instanceLabel) this.instanceLabel.textContent = `instancia ${this.instanceId}`;
      this.bind();
      this.bindEditor();
    }

    bind(){
      this.btnPrev.addEventListener('click', () => { if (this.page>1){ this.page--; this.load(); } });
      this.btnNext.addEventListener('click', () => { this.page++; this.load(); });
      this.btnRefresh.addEventListener('click', () => this.load());
      this.btnUse.addEventListener('click', () => this.applySelection());
      this.btnClear.addEventListener('click', () => this.applySelection(true));
      this.btnClose.addEventListener('click', () => this.close());
      this.backdrop.addEventListener('click', () => this.close());

      this.search.addEventListener('input', (e) => { this.q = e.target.value; this.page=1; this.loadDebounced(); });
      this.selType.addEventListener('change', (e) => { this.type = e.target.value; this.page=1; this.load(); });

      this.fileInput.addEventListener('change', (e) => {
        if (e.target.files?.length) this.uploadFile(e.target.files[0]);
        e.target.value = '';
      });

      this.btnAddUrl.addEventListener('click', () => this.addExternalUrl());
    }

    // Editor panel bindings
    bindEditor(){
      const ed = $('#mf-editor');
      $('[data-ed="close"]', ed).addEventListener('click', ()=>this.closeEditor());
      $('[data-ed="cancel"]', ed).addEventListener('click', ()=>this.closeEditor());
      $('[data-ed="save"]', ed).addEventListener('click', ()=>this.saveEditor());
      $('[data-ed="delete"]', ed).addEventListener('click', ()=>this.deleteCurrent());
    }

    open(){ this.root.classList.remove('hidden'); this.load(); }
    close(){ this.root.classList.add('hidden'); }

    // ====== Skeleton + empty ======
    renderSkeleton(count=12){
      return Array.from({length:count}).map(()=>`
        <div class="rounded-xl border border-slate-200/60 dark:border-slate-700/60 overflow-hidden animate-pulse">
          <div class="aspect-square bg-slate-200/60 dark:bg-slate-700/60"></div>
          <div class="p-2 space-y-2">
            <div class="h-3 rounded bg-slate-200/80 dark:bg-slate-700/80"></div>
            <div class="h-3 w-1/2 rounded bg-slate-200/70 dark:bg-slate-700/70"></div>
          </div>
        </div>
      `).join('');
    }

    renderEmpty(){
      return `
        <div class="col-span-full py-14 text-center">
          <div class="text-5xl mb-2">🗂️</div>
          <div class="text-base font-medium">No hay medios aún</div>
          <div class="text-sm text-muted">Sube un archivo o agrega una URL externa</div>
        </div>
      `;
    }

    loadDebounced(){ clearTimeout(this._t); this._t=setTimeout(()=>this.load(), 300); }

    async load(){
      this.grid.innerHTML = this.renderSkeleton();
      try {
        const params = new URLSearchParams({ page: this.page, per_page: this.perPage });
        if (this.q) params.append('q', this.q);
        if (this.type) params.append('type', this.type);

        const res = await fetch(`${API}?${params.toString()}`, { headers: headers() });
        if (!res.ok) throw new Error('Error al listar medios');
        const data = await res.json();

        this.pageInfo.textContent = `Página ${data.current_page} de ${data.last_page || 1}`;
        this.btnPrev.disabled = data.current_page <= 1;
        this.btnNext.disabled = data.current_page >= (data.last_page || 1);

        const items = data.data || [];
        if (!items.length) {
          this.grid.innerHTML = this.renderEmpty();
        } else {
          this.grid.innerHTML = items.map(it => this.renderItem(it)).join('');
          $$('[data-mf-item]', this.grid).forEach(card => {
            card.addEventListener('click', () => this.toggle(card.dataset.id));
          });
          $$('[data-mf-edit]', this.grid).forEach(btn => {
            btn.addEventListener('click', (e) => {
              e.stopPropagation();
              const id = btn.getAttribute('data-mf-edit');
              this.openEditor(id);
            });
          });
        }
      } catch (err){
        console.error(err);
        this.grid.innerHTML = `<div class="col-span-full text-center text-sm text-red-500">No se pudieron cargar los medios.</div>`;
      }
    }

    toggle(id){
      if (this.mode === 'single') {
        this.selected.clear(); this.selected.add(id);
        $$('[data-mf-item].mf-active', this.grid).forEach(el => el.classList.remove('mf-active','ring-2','ring-brand-500'));
        const card = $(`[data-mf-item][data-id="${id}"]`, this.grid);
        card?.classList.add('mf-active','ring-2','ring-brand-500');
        this.selInfo.textContent = '1 seleccionado';
      } else {
        if (this.selected.has(id)) this.selected.delete(id); else this.selected.add(id);
        const card = $(`[data-mf-item][data-id="${id}"]`, this.grid);
        card?.classList.toggle('mf-active'); card?.classList.toggle('ring-2'); card?.classList.toggle('ring-brand-500');
        this.selInfo.textContent = `${this.selected.size} seleccionados`;
      }
    }

    applySelection(clear=false){
      if (clear){ this.selected.clear(); }
      const ids = Array.from(this.selected);
      if (this.mode === 'single') this.input.value = clear ? '' : (ids[0] || '');
      else this.input.value = clear ? '' : ids.join(',');

      this.renderPreview(ids);
      if (!clear) { this.close(); toast('Selección aplicada'); }
      else { this.selInfo.textContent = '0 seleccionados'; }
    }

    async renderPreview(ids){
      if (!this.previewEl) return;
      this.previewEl.innerHTML = '';
      if (!ids.length){ this.previewEl.innerHTML = `<div class="text-sm text-muted">Sin selección</div>`; return; }
      const loaders = ids.slice(0, 24).map(id => fetch(`${API}/${id}`, { headers: headers() }).then(r=>r.ok?r.json():null).catch(()=>null));
      const items = (await Promise.all(loaders)).filter(Boolean);

      const card = (it, big=false) => {
        const thumb = this.thumbFor(it);
        const size = formatBytes(it.size_bytes);
        const base = 'rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60';
        return `
          <div class="${base}">
            <div class="${big?'aspect-video':'aspect-square'} bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
              ${thumb}
            </div>
            <div class="p-2 ${big?'text-sm':'text-xs'}">
              <div class="font-medium line-clamp-1">#${it.id} • ${it.type||''}</div>
              <div class="text-muted">${size||''}</div>
            </div>
          </div>`;
      };

      if (this.mode === 'single'){ this.previewEl.innerHTML = card(items[0], true); }
      else {
        this.previewEl.classList.add('grid','grid-cols-2','md:grid-cols-4','gap-3');
        this.previewEl.innerHTML = items.map(it=>card(it,false)).join('');
      }
    }

    thumbFor(it){
      const url = it.url || ''; const t = it.type;
      if (t === 'image') return `<img src="${url}" alt="" class="w-full h-full object-cover">`;
      if (t === 'video') return `<div class="text-center text-sm"><div class="text-3xl">🎬</div><div class="text-muted">${it.provider||'video'}</div></div>`;
      if (t === 'audio') return `<div class="text-4xl">🎵</div>`;
      return `<div class="text-4xl">📄</div>`;
    }

    renderItem(it){
      const thumb = this.thumbFor(it);
      const size = formatBytes(it.size_bytes);
      const id = it.id;
      return `
        <div class="group relative">
          <button type="button"
                  class="w-full text-left rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60 hover:shadow-soft transition ring-offset-2"
                  data-mf-item data-id="${id}">
            <div class="aspect-square bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
              ${thumb}
            </div>
            <div class="p-2">
              <div class="text-xs font-medium line-clamp-1">#${id} • ${it.type||''}</div>
              <div class="text-[11px] text-muted">${size||''}</div>
            </div>
          </button>

          <!-- Botón acciones -->
          <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
            <button type="button" class="px-2 py-1 rounded-md bg-white/90 dark:bg-slate-900/80 border border-slate-200/60 dark:border-slate-700/60 shadow"
                    data-mf-edit="${id}" aria-label="Editar">⋯</button>
          </div>
        </div>
      `;
    }

    async uploadFile(file){
      const fd = new FormData();
      fd.append('file', file);
      try {
        const res = await fetch(API, { method:'POST', headers: headers(), body: fd });
        if (!res.ok) throw new Error('Error al subir archivo');
        await res.json();
        toast('Archivo subido');
        this.page = 1; this.load();
      } catch (e){ console.error(e); toast('No se pudo subir', 2600); }
    }

    async addExternalUrl(){
      const url = this.extUrl.value.trim();
      const type = this.extType.value;
      const provider = (this.extProv.value || 'external').trim() || 'external';
      if (!url) return toast('Ingresa una URL válida');
      try {
        const res = await fetch(API, {
          method:'POST',
          headers: { ...headers(), 'Content-Type': 'application/json' },
          body: JSON.stringify({ url, type, provider })
        });
        if (!res.ok) throw new Error('Error al agregar URL');
        await res.json();
        this.extUrl.value=''; toast('URL agregada'); this.page=1; this.load();
      } catch (e){ console.error(e); toast('No se pudo agregar URL', 2600); }
    }

    // === Editor ===
    openEditor(id){
      this.loadEditor(id);
      $('#mf-editor').classList.remove('translate-x-full');
    }
    closeEditor(){
      $('#mf-editor').classList.add('translate-x-full');
    }

    async loadEditor(id){
      const ed = $('#mf-editor');
      ed.dataset.currentId = id;
      try{
        const res = await fetch(`${API}/${id}`, { headers: headers() });
        if(!res.ok) throw new Error('No se pudo cargar');
        const it = await res.json();

        $('[data-ed="id"]', ed).value = it.id;
        $('[data-ed="type"]', ed).value = it.type || 'document';
        $('[data-ed="provider"]', ed).value = it.provider || '';
        $('[data-ed="url"]', ed).value = it.url || '';

        $('[data-ed="thumb"]', ed).innerHTML = `
          <div class="aspect-video bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
            ${this.thumbFor(it)}
          </div>
          <div class="p-2 text-xs text-muted">#${it.id} • ${it.mime_type||''} • ${formatBytes(it.size_bytes)||''}</div>
        `;
      }catch(e){ console.error(e); toast('No se pudo abrir el editor', 2400); }
    }

    async saveEditor(){
      const ed = $('#mf-editor');
      const id = ed.dataset.currentId;
      const type = $('[data-ed="type"]', ed).value;
      const provider = $('[data-ed="provider"]', ed).value.trim();
      const url = $('[data-ed="url"]', ed).value.trim();
      const file = $('[data-ed="file"]', ed).files[0];

      try{
        let res;
        if (file){
          const fd = new FormData();
          fd.append('file', file);
          if (type) fd.append('type', type);
          if (provider) fd.append('provider', provider);
          if (url) fd.append('url', url);
          res = await fetch(`${API}/${id}`, { method:'PATCH', headers: headers(), body: fd });
        } else if (url || type || provider){
          res = await fetch(`${API}/${id}`, {
            method:'PATCH',
            headers: { ...headers(), 'Content-Type': 'application/json' },
            body: JSON.stringify({ ...(url?{url}:{}), ...(type?{type}:{}), ...(provider?{provider}:{}) })
          });
        } else {
          toast('Nada para guardar'); return;
        }
        if(!res.ok) throw new Error('Error al guardar');
        await res.json();
        toast('Cambios guardados');
        this.closeEditor();
        this.load();
      }catch(e){ console.error(e); toast('No se pudieron guardar los cambios', 2600); }
    }

    async deleteCurrent(){
      const ed = $('#mf-editor');
      const id = ed.dataset.currentId;
      if (!confirm('¿Eliminar este archivo? Esta acción no se puede deshacer.')) return;
      try{
        const res = await fetch(`${API}/${id}`, { method:'DELETE', headers: headers() });
        if(!res.ok) throw new Error('Error al eliminar');
        toast('Eliminado');
        this.closeEditor();
        this.selected.delete(String(id));
        this.selInfo.textContent = `${this.selected.size} seleccionados`;
        this.load();
      }catch(e){ console.error(e); toast('No se pudo eliminar', 2600); }
    }
  }

  // Instancias por input
  const instances = new Map();
  const openFor = (input) => {
    const id = input.id || uid('input'); if (!input.id) input.id = id;
    if (!instances.has(id)) instances.set(id, new MediaFinder({input}));
    instances.get(id).open();
  };

  // Botón abrir
  $$('[data-fp-open]').forEach(btn => btn.addEventListener('click', () => {
    const sel = btn.getAttribute('data-fp-open'); const input = document.querySelector(sel); if (!input) return; openFor(input);
  }));

  // Doble click en input
  $$('input[data-filepicker]').forEach(inp => { inp.addEventListener('dblclick', () => openFor(inp)); });
})();
</script>
@endpush
@endsection
