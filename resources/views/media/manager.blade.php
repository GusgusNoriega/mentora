@extends('dashboard')
@section('title', 'Administrador de Archivos')

@section('content')
<div class="grid gap-6">
  <h1 class="text-2xl font-semibold">Administrador de archivos</h1>

  {{-- DEMO: selector ÚNICO --}}
  <div data-fp-scope class="rounded-2xl border border-slate-200/60 dark:border-slate-700/60 p-4 bg-white dark:bg-slate-900">
    <h2 class="font-medium mb-3">Selector único</h2>
    <div class="flex items-center gap-3">
      <input
        type="text"
        class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800"
        placeholder="ID del archivo"
        readonly
        data-filepicker="single"
        data-fp-max="1"
        data-fp-preview="#archive_manager_single_preview"
        data-fp-per-page="10"
      >
      <button type="button" class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow" data-fp-open>
        Seleccionar
      </button>
    </div>
    <div id="archive_manager_single_preview" class="mt-3"></div>
  </div>

  {{-- DEMO: selector MÚLTIPLE --}}
  <div data-fp-scope class="rounded-2xl border border-slate-200/60 dark:border-slate-700/60 p-4 bg-white dark:bg-slate-900">
    <h2 class="font-medium mb-3">Selector múltiple (hasta 3)</h2>
    <div class="flex items-center gap-3">
      <input
        type="text"
        class="w-full rounded-lg border px-3 py-2 bg-white dark:bg-slate-800"
        placeholder="IDs separados por comas"
        readonly
        data-filepicker="multiple"
        data-fp-max="3"
        data-fp-preview="#archive_manager_multi_preview"
        data-fp-per-page="10"
      >
      <button type="button" class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow" data-fp-open>
        Seleccionar
      </button>
    </div>
    {{-- preview del input: mini thumbs que respetan proporción --}}
    <div id="archive_manager_multi_preview" class="mt-3 grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-2"></div>
  </div>
</div>

{{-- MODAL raíz (biblioteca) --}}
<div id="archive_manager-root" class="fixed inset-0 z-[9999] hidden" aria-modal="true" role="dialog">
  <div class="fixed inset-0 bg-black/45 backdrop-blur-sm" data-mf="backdrop"></div>

  <div class="relative mx-auto my-0 w-screen max-w-[98vw] md:my-10 md:w-[98vw]">
    {{-- Móvil 100vh; Escritorio altura fija para que NO se salga --}}
    <div class="rounded-none md:rounded-2xl border border-white/10 bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl shadow-2xl overflow-hidden flex flex-col h-[100vh] md:h-[80vh] md:max-h-[80vh]">
      {{-- Header --}}
      <div class="flex items-center justify-between px-3 md:px-4 py-3 border-b border-slate-200/60 dark:border-slate-700/60">
        <div class="flex items-center gap-3">
          <div class="text-lg font-semibold">Biblioteca de medios</div>
          <span class="hidden md:inline text-xs px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500" data-mf="instance"></span>
        </div>
        <button type="button" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" data-mf="close" aria-label="Cerrar">✕</button>
      </div>

      {{-- Toolbar (sticky) --}}
      <div class="px-3 md:px-4 py-3 border-b border-slate-200/60 dark:border-slate-700/60 sticky top-0 bg-white/90 dark:bg-slate-900/80 backdrop-blur-xl z-10">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3 items-stretch">
          <div class="flex flex-wrap gap-2 content-start">
            <input type="text" data-mf="search" placeholder="Buscar por nombre, URL, MIME…" class="min-w-[160px] flex-1 rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" aria-label="Buscar">
            <select data-mf="type" class="rounded-lg border px-3 py-2 bg-white dark:bg-slate-800" aria-label="Filtrar por tipo">
              <option value="">Todos</option>
              <option value="image">Imágenes</option>
              <option value="video">Videos</option>
              <option value="audio">Audios</option>
              <option value="document">Documentos</option>
            </select>
            <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-mf="refresh">Actualizar</button>
          </div>
          <div class="flex flex-wrap gap-2 content-start">
            <button class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow" data-mf="open-upload">Subir archivo</button>
            <button class="px-3 py-2 rounded-lg bg-accent text-white hover:opacity-90 shadow" data-mf="open-url">Agregar URL / Video</button>
          </div>
        </div>
      </div>

      {{-- Cuerpo con scroll interno y sin desbordes --}}
      <div class="px-3 md:px-4 pt-3 pb-2 flex-1 min-h-0 overflow-y-auto">
        {{-- Panel seleccionados: ahora respeta el tamaño de la imagen (object-contain) y tiene scroll --}}
        <div class="mb-3 rounded-xl border border-slate-200/60 dark:border-slate-700/60 p-3 bg-white/70 dark:bg-slate-900/60 hidden" data-mf="picked-wrap">
          <div class="flex items-center justify-between mb-2">
            <div class="text-sm font-medium">Archivos seleccionados</div>
            <button type="button" class="text-xs md:text-sm px-2 py-1 rounded bg-slate-100 dark:bg-slate-800" data-mf="picked-clear">Quitar todo</button>
          </div>
          <div class="max-h-36 md:max-h-28 ">
            {{-- grid densa; cada celda centra la imagen y NO la recorta --}}
            <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2 min-w-0" data-mf="picked" role="list"></div>
          </div>
        </div>

        {{-- Grilla principal (se mantiene cover para cards bonitas) --}}
        <div class="h-[calc(100vh-20rem)] md:h-[calc(80vh-14rem)] max-h-full ">
          <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3" data-mf="grid" role="list"></div>

          <div class="flex justify-between items-center mt-4 pb-2">
            <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 disabled:opacity-50" data-mf="prev">Anterior</button>
            <div class="text-sm text-slate-500" data-mf="pageinfo">Página 1</div>
            <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800 disabled:opacity-50" data-mf="next">Siguiente</button>
          </div>
        </div>
      </div>

      {{-- Footer --}}
      <div class="px-3 md:px-4 py-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between">
        <div class="text-sm text-slate-500" data-mf="selection">0 seleccionados</div>
        <div class="flex items-center gap-2">
          <button class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-800" data-mf="clear">Limpiar</button>
          <button class="px-3 py-2 rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow" data-mf="use">Usar selección</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Panel Editor --}}
  <div id="archive_manager-editor" class="fixed right-0 top-0 h-full w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl border-l border-slate-200/60 dark:border-slate-700/60 translate-x-full transition-transform duration-200 z-[75] flex flex-col">
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
  <div id="archive_manager-toast" class="fixed left-1/2 -translate-x-1/2 bottom-6 px-4 py-2 rounded-xl text-white bg-slate-900/90 hidden" role="status" aria-live="polite"></div>
</div>

{{-- MODAL: Subir archivo --}}
<div id="archive_manager-upload" class="fixed inset-0 z-[10000] hidden">
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
<div id="archive_manager-url" class="fixed inset-0 z-[10000] hidden">
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

  const token = $('meta[name="api-token"]')?.getAttribute('content') || '';
  const csrf  = $('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const API   = '/mentora/public/api/media';

  const headers = () => {
    const h = { Accept: 'application/json' };
    if (token) h['Authorization'] = `Bearer ${token}`;
    if (csrf)  h['X-CSRF-TOKEN'] = csrf;
    return h;
  };

  const uid = (p='archive_manager') =>
    `${p}_${Date.now().toString(36)}_${Math.random().toString(36).slice(2,8)}`;

  const formatBytes = (b) => {
    if (b == null) return '';
    const u=['B','KB','MB','GB']; let i=0, n=b;
    while (n>=1024 && i<u.length-1) { n/=1024; i++; }
    return `${n.toFixed(1)} ${u[i]}`;
  };

  const toast = (msg, ms=2200) => {
    const t = $('#archive_manager-toast') || $('#archive_manager-root #archive_manager-toast');
    if (!t) return;
    t.textContent = msg; t.classList.remove('hidden');
    clearTimeout(t._h); t._h = setTimeout(() => t.classList.add('hidden'), ms);
  };

  const lockScroll = (on=true) => {
    document.documentElement.style.overflow = on ? 'hidden' : '';
    document.body.style.overflow = on ? 'hidden' : '';
  };

  const animateIn  = (card) => { if (!card) return; requestAnimationFrame(() => { card.classList.remove('translate-y-2','opacity-0'); card.classList.add('opacity-100','translate-y-0'); }); };
  const animateOut = (card, done) => { if (!card) return done?.(); card.classList.remove('opacity-100','translate-y-0'); card.classList.add('translate-y-2','opacity-0'); setTimeout(() => done?.(), 180); };
  const openModal  = (id) => { const m=$(id); if(!m) return; m.classList.remove('hidden'); const c=m.querySelector('.transform'); if (c){ c.classList.add('translate-y-2','opacity-0'); animateIn(c);} lockScroll(true); };
  const closeModal = (id) => { const m=$(id); if(!m) return; const c=m.querySelector('.transform'); if(c){ animateOut(c,()=>{ m.classList.add('hidden'); lockScroll(false);}); } else { m.classList.add('hidden'); lockScroll(false);} };

  // ====== MediaFinder ======
  class MediaFinder {
    static active = null;
    static globalsBound = false;

    constructor({ input }) {
      this.input = input;
      this.instanceId = uid();

      // Config
      this.mode = (input.getAttribute('data-filepicker') || 'single').toLowerCase();
      this.max  = (() => {
        const raw = input.getAttribute('data-fp-max');
        if (raw == null) return this.mode === 'multiple' ? Infinity : 1;
        const n = parseInt(raw, 10);
        return Number.isFinite(n) && n>0 ? n : (this.mode === 'multiple' ? Infinity : 1);
      })();
      const perAttr = parseInt(input.getAttribute('data-fp-per-page') || '10', 10);
      this.perPage  = Number.isFinite(perAttr) ? Math.max(1, Math.min(50, perAttr)) : 10;

      this.previewSelector = input.getAttribute('data-fp-preview') || '';
      this.previewEl = this.previewSelector ? $(this.previewSelector) : null;

      this.q=''; this.type=''; this.page=1; this.selected = new Set();

      // DOM
      this.root=$('#archive_manager-root');
      this.grid=$('[data-mf="grid"]',this.root);
      this.pageInfo=$('[data-mf="pageinfo"]',this.root);
      this.selInfo=$('[data-mf="selection"]',this.root);
      this.instanceLabel=$('[data-mf="instance"]',this.root);
      this.pickedWrap=$('[data-mf="picked-wrap"]',this.root);
      this.pickedGrid=$('[data-mf="picked"]',this.root);

      this._abort=null;
      if (this.instanceLabel) this.instanceLabel.textContent = `instancia ${this.instanceId}`;
      if (!MediaFinder.globalsBound) { MediaFinder.bindGlobalHandlers(); MediaFinder.globalsBound = true; }
    }

    static bindGlobalHandlers(){
      const root=$('#archive_manager-root');
      const uploadModal=$('#archive_manager-upload');
      const urlModal=$('#archive_manager-url');
      const editor=$('#archive_manager-editor');

      $('[data-mf="prev"]',root)?.addEventListener('click',()=>{const i=MediaFinder.active; if(i&&i.page>1){i.page--;i.load();}});
      $('[data-mf="next"]',root)?.addEventListener('click',()=>{const i=MediaFinder.active; if(i){i.page++;i.load();}});
      $('[data-mf="refresh"]',root)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.load();});
      $('[data-mf="use"]',root)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.applySelection();});
      $('[data-mf="clear"]',root)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.applySelection(true);});
      $('[data-mf="close"]',root)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.close();});
      $('[data-mf="backdrop"]',root)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.close();});

      $('[data-mf="search"]',root)?.addEventListener('input',(e)=>{const i=MediaFinder.active; if(!i) return; i.q=e.target.value; i.page=1; i.loadDebounced();});
      $('[data-mf="type"]',root)?.addEventListener('change',(e)=>{const i=MediaFinder.active; if(!i) return; i.type=e.target.value; i.page=1; i.load();});

      $('[data-mf="open-upload"]',root)?.addEventListener('click',()=>openModal('#archive_manager-upload'));
      $('[data-mf="open-url"]',root)?.addEventListener('click',()=>openModal('#archive_manager-url'));

      $('[data-up="save"]',uploadModal)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.uploadFromModal();});
      ['cancel','close','backdrop'].forEach(k=>{$(`[data-up="${k}"]`,uploadModal)?.addEventListener('click',()=>closeModal('#archive_manager-upload'));});

      $('[data-ur="save"]',urlModal)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.addUrlFromModal();});
      ['cancel','close','backdrop'].forEach(k=>{$(`[data-ur="${k}"]`,urlModal)?.addEventListener('click',()=>closeModal('#archive_manager-url'));});

      $('[data-ed="save"]',editor)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.saveEditor();});
      $('[data-ed="delete"]',editor)?.addEventListener('click',()=>{const i=MediaFinder.active; i&&i.deleteCurrent();});
      ['close','cancel'].forEach(k=>{$(`[data-ed="${k}"]`,editor)?.addEventListener('click',()=>$('#archive_manager-editor').classList.add('translate-x-full'));});

      document.addEventListener('keydown',(e)=>{ if(e.key!=='Escape') return;
        if (!$('#archive_manager-upload').classList.contains('hidden')) { closeModal('#archive_manager-upload'); return; }
        if (!$('#archive_manager-url').classList.contains('hidden'))   { closeModal('#archive_manager-url');   return; }
        if (!$('#archive_manager-root').classList.contains('hidden'))  { const i=MediaFinder.active; i&&i.close(); }
      });
    }

    // === estado/IO ===
    syncFromInput(){
      const raw=(this.input.value||'').trim();
      const ids=raw?raw.split(',').map(s=>s.trim()).filter(Boolean):[];
      this.selected=new Set(ids);
      if (Number.isFinite(this.max) && this.selected.size>this.max){
        this.selected=new Set(Array.from(this.selected).slice(0,this.max));
        toast(`Se limitaron a ${this.max} elementos (límite del campo)`);
      }
      const c=this.selected.size; this.selInfo.textContent=`${c} seleccionado${c===1?'':'s'}`;
    }

    open(){ MediaFinder.active=this; this.root.classList.remove('hidden'); lockScroll(true); this.syncFromInput(); this.renderPicked(); this.load(); }
    close(){ this.root.classList.add('hidden'); lockScroll(false); if(this._abort){this._abort.abort(); this._abort=null;} }

    // === UI helpers ===
    renderSkeleton(n=12){
      return Array.from({length:n}).map(()=>`
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
          <div class="text-sm text-slate-500">Sube un archivo o agrega una URL externa</div>
        </div>
      `;
    }
    loadDebounced(){ clearTimeout(this._t); this._t=setTimeout(()=>this.load(),300); }

    updateDisabledState(){
      if(!this.grid) return;
      if (this.mode!=='multiple' || !Number.isFinite(this.max)) return;
      const atMax=this.selected.size>=this.max;
      $$('[data-mf-item]',this.grid).forEach(el=>{
        const act=el.classList.contains('mf-active');
        if(atMax && !act){ el.classList.add('pointer-events-none','opacity-50'); el.setAttribute('aria-disabled','true'); }
        else { el.classList.remove('pointer-events-none','opacity-50'); el.removeAttribute('aria-disabled'); }
      });
    }

    async load(){
      if(!this.grid) return;
      if(this._abort) this._abort.abort();
      this._abort=new AbortController();
      const {signal}=this._abort;

      this.grid.innerHTML=this.renderSkeleton();
      try{
        const p=new URLSearchParams({ page:String(this.page), per_page:String(this.perPage) });
        if(this.q)   p.append('q',this.q);
        if(this.type)p.append('type',this.type);

        const res=await fetch(`${API}?${p.toString()}`,{ headers:headers(), signal });
        if(!res.ok) throw new Error('Error al listar medios');
        const data=await res.json();

        this.pageInfo.textContent=`Página ${data.current_page} de ${data.last_page||1}`;
        $('[data-mf="prev"]').disabled = data.current_page <= 1;
        $('[data-mf="next"]').disabled = data.current_page >= (data.last_page || 1);

        const items=data.data||[];
        if (!items.length){ this.grid.innerHTML=this.renderEmpty(); }
        else{
          this.grid.innerHTML = items.map(it => this.renderItem(it)).join('');

          $$('[data-mf-item]',this.grid).forEach(card=>{
            card.addEventListener('click',()=>this.toggle(card.dataset.id));
          });

          $$('[data-mf-item]',this.grid).forEach(card=>{
            if(this.selected.has(card.dataset.id)){
              card.classList.add('mf-active','ring-2','ring-brand-500');
              card.setAttribute('aria-pressed','true');
            } else { card.setAttribute('aria-pressed','false'); }
          });

          $$('[data-mf-edit]',this.grid).forEach(btn=>{
            btn.addEventListener('click',(e)=>{ e.stopPropagation(); const id=btn.getAttribute('data-mf-edit'); this.openEditor(id); });
          });

          this.updateDisabledState();
        }
      }catch(err){
        if (err.name==='AbortError') return;
        console.error(err);
        this.grid.innerHTML = `<div class="col-span-full text-center text-sm text-red-500">No se pudieron cargar los medios.</div>`;
      } finally { this._abort=null; }
    }

    // ==== PICKED (miniaturas que respetan tamaño/proporción) ====
    async renderPicked(){
      const wrap=this.pickedWrap, grid=this.pickedGrid;
      if(!wrap || !grid) return;
      const ids=Array.from(this.selected);
      if(!ids.length){ wrap.classList.add('hidden'); grid.innerHTML=''; return; }
      wrap.classList.remove('hidden');

      const items=(await Promise.all(ids.slice(0,100).map(id=>fetch(`${API}/${id}`,{headers:headers()}).then(r=>r.ok?r.json():null).catch(()=>null)))).filter(Boolean);

      const card = (it) => {
        const thumb = this.thumbFor(it, 'contain'); // 👈 importante: contain
        return `
          <div class="relative border border-slate-200/60 dark:border-slate-700/60 rounded-lg overflow-hidden" role="listitem" data-pk="${it.id}">
            <button type="button" class="absolute -top-1 -right-1 z-10 px-1.5 py-0.5 rounded-md bg-white/90 dark:bg-slate-900/80 border border-slate-200/60 dark:border-slate-700/60 text-[10px] shadow" data-pk-remove="${it.id}" aria-label="Quitar">✕</button>
            <!-- Alturas fijas por breakpoint; la imagen se adapta sin recortarse -->
            <div class="w-full h-14 sm:h-16 md:h-20 lg:h-24 bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
              ${thumb}
            </div>
          </div>
        `;
      };

      grid.innerHTML = items.map(card).join('');
      $$('[data-pk-remove]',grid).forEach(btn=>{
        btn.addEventListener('click',(e)=>{ e.stopPropagation(); const id=btn.getAttribute('data-pk-remove'); this.removeFromSelection(id); });
      });
    }

    removeFromSelection(id){
      if(!this.selected.has(String(id))) return;
      this.selected.delete(String(id));
      const ids=Array.from(this.selected);
      this.input.value = (this.mode==='single'||this.max===1) ? (ids[0]||'') : ids.join(',');
      const c=this.selected.size; this.selInfo.textContent=`${c} seleccionado${c===1?'':'s'}`;
      const card=$(`[data-mf-item][data-id="${id}"]`,this.grid); if(card){ card.classList.remove('mf-active','ring-2','ring-brand-500'); card.setAttribute('aria-pressed','false'); }
      this.updateDisabledState(); this.renderPicked();
    }

    toggle(id){
      if (this.mode==='single' || this.max===1){
        this.selected.clear(); this.selected.add(id);
        $$('[data-mf-item].mf-active',this.grid).forEach(el=>{el.classList.remove('mf-active','ring-2','ring-brand-500'); el.setAttribute('aria-pressed','false');});
        const c=$(`[data-mf-item][data-id="${id}"]`,this.grid);
        if(c){ c.classList.add('mf-active','ring-2','ring-brand-500'); c.setAttribute('aria-pressed','true'); }
        this.selInfo.textContent='1 seleccionado';
        this.input.value=id; this.renderPicked(); this.updateDisabledState(); return;
      }
      const already=this.selected.has(id);
      if(!already && this.selected.size>=this.max){ toast(`Límite: puedes seleccionar hasta ${this.max}`); this.updateDisabledState(); return; }
      if (already) this.selected.delete(id); else this.selected.add(id);

      const card=$(`[data-mf-item][data-id="${id}"]`,this.grid);
      if(card){
        card.classList.toggle('mf-active'); card.classList.toggle('ring-2'); card.classList.toggle('ring-brand-500');
        card.setAttribute('aria-pressed', card.classList.contains('mf-active') ? 'true' : 'false');
      }

      const ids=Array.from(this.selected);
      this.input.value=ids.join(',');
      this.renderPicked();
      this.selInfo.textContent=`${this.selected.size} seleccionados`;
      this.updateDisabledState();
    }

    applySelection(clear=false){
      if (clear) this.selected.clear();
      let ids=Array.from(this.selected);
      if (this.mode!=='single' && Number.isFinite(this.max) && ids.length>this.max){
        ids=ids.slice(0,this.max); this.selected=new Set(ids); toast(`Se aplicaron solo ${this.max} elementos (límite)`);
      }
      this.input.value = (this.mode==='single'||this.max===1) ? (clear?'':(ids[0]||'')) : (clear?'':ids.join(','));
      this.renderPreview(ids);
      if (!clear){ this.close(); toast('Selección aplicada'); } else { this.selInfo.textContent='0 seleccionados'; }
      this.updateDisabledState(); this.renderPicked();
    }

    // ==== PREVIEW de inputs (también contain y mini) ====
    async renderPreview(ids){
      if(!this.previewEl) return;
      this.previewEl.innerHTML='';
      if(!ids.length){ this.previewEl.innerHTML = `<div class="text-sm text-slate-500">Sin selección</div>`; return; }

      const items=(await Promise.all(ids.slice(0,48).map(id=>fetch(`${API}/${id}`,{headers:headers()}).then(r=>r.ok?r.json():null).catch(()=>null)))).filter(Boolean);

      const card = (it) => {
        const thumb = this.thumbFor(it, 'contain'); // 👈 contain
        return `
          <div class="rounded-lg overflow-hidden border border-slate-200/60 dark:border-slate-700/60">
            <div class="w-full h-14 sm:h-16 md:h-20 bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
              ${thumb}
            </div>
          </div>
        `;
      };

      this.previewEl.classList.add('grid','grid-cols-4','sm:grid-cols-6','md:grid-cols-8','gap-2');
      this.previewEl.innerHTML = items.map(card).join('');
    }

    // fit: 'cover' (cards) | 'contain' (miniaturas que respetan tamaño)
    thumbFor(it, fit='cover'){
      const url = it.url || '';
      const t = it.type;
      if (t === 'image'){
        if (fit === 'contain') return `<img src="${url}" alt="" class="max-h-full max-w-full object-contain">`;
        return `<img src="${url}" alt="" class="w-full h-full object-cover">`;
      }
      if (t === 'video') return `<div class="text-center text-xs"><div class="text-2xl">🎬</div><div class="text-slate-500">${it.provider||'video'}</div></div>`;
      if (t === 'audio') return `<div class="text-2xl">🎵</div>`;
      return `<div class="text-2xl">📄</div>`;
    }

    // grid principal (cards cuadradas con cover)
    renderItem(it){
      const thumb = this.thumbFor(it, 'cover');
      const size  = formatBytes(it.size_bytes);
      const id    = it.id;
      const name  = it.name ? `<div class="text-[11px] text-slate-700 dark:text-slate-300 line-clamp-1" title="${it.name}">${it.name}</div>` : '';
      return `
        <div class="group relative" role="listitem">
          <button type="button" class="w-full text-left rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-700/60 hover:shadow transition ring-offset-2" data-mf-item data-id="${id}" aria-pressed="false">
            <div class="aspect-square bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
              ${thumb}
            </div>
            <div class="p-2">
              <div class="text-xs font-medium line-clamp-1">#${id} • ${it.type||''}</div>
              ${name}
              <div class="text-[11px] text-slate-500">${size||''}</div>
            </div>
          </button>
          <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
            <button type="button" class="px-2 py-1 rounded-md bg-white/90 dark:bg-slate-900/80 border border-slate-200/60 dark:border-slate-700/60 shadow" data-mf-edit="${id}" aria-label="Editar">⋯</button>
          </div>
        </div>
      `;
    }

    // === Subir / URL / Editor (igual que antes) ===
    async uploadFromModal(){
      const up=$('#archive_manager-upload');
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
        closeModal('#archive_manager-upload'); this.page=1; this.load();
      }catch(e){ console.error(e); toast('No se pudo subir',2600); }
    }

    async addUrlFromModal(){
      const um=$('#archive_manager-url');
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
        closeModal('#archive_manager-url'); toast('URL agregada'); this.page=1; this.load();
      }catch(e){ console.error(e); toast('No se pudo agregar URL',2600); }
    }

    openEditor(id){ this.loadEditor(id); $('#archive_manager-editor').classList.remove('translate-x-full'); }
    closeEditor(){ $('#archive_manager-editor').classList.add('translate-x-full'); }
    toggleVideoUrlVisibility(isVideo){ const wrap=$('[data-ed="url-wrap"]',$('#archive_manager-editor')); if(!wrap) return; wrap.classList.toggle('hidden',!isVideo); }

    async loadEditor(id){
      const ed=$('#archive_manager-editor'); ed.dataset.currentId=id;
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
        this.toggleVideoUrlVisibility((it.type||'')==='video');
        $('[data-ed="thumb"]',ed).innerHTML = `
          <div class="aspect-video bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden">
            ${this.thumbFor(it,'cover')}
          </div>
          <div class="p-2 text-xs text-slate-500">#${it.id} • ${it.mime_type||''} • ${formatBytes(it.size_bytes)||''}</div>
        `;
      }catch(e){ console.error(e); toast('No se pudo abrir el editor',2400); }
    }

    async saveEditor(){
      const ed=$('#archive_manager-editor');
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
        await res.json(); toast('Cambios guardados'); this.closeEditor(); this.load();
      }catch(e){ console.error(e); toast('No se pudieron guardar los cambios',2600); }
    }

    async deleteCurrent(){
      const ed=$('#archive_manager-editor'); const id=ed.dataset.currentId;
      if(!confirm('¿Eliminar este archivo? Esta acción no se puede deshacer.')) return;
      try{
        const res=await fetch(`${API}/${id}`,{method:'DELETE',headers:headers()});
        if(!res.ok) throw new Error('Error al eliminar');
        toast('Eliminado'); this.closeEditor();
        this.selected.delete(String(id));
        this.selInfo.textContent=`${this.selected.size} seleccionados`;
        this.input.value=Array.from(this.selected).join(',');
        this.renderPicked();
        const card=$(`[data-mf-item][data-id="${id}"]`,this.grid);
        card?.classList.remove('mf-active','ring-2','ring-brand-500');
        this.load();
      }catch(e){ console.error(e); toast('No se pudo eliminar',2600); }
    }
  }

  // Instancias por input
  const instances=new Map();
  const openFor=(input)=>{ const id=input.id||uid('input'); if(!input.id) input.id=id; if(!instances.has(id)) instances.set(id,new MediaFinder({input})); instances.get(id).open(); };

  // Abrir desde botón
  $$('[data-fp-open]').forEach(btn=>btn.addEventListener('click',()=>{
    const explicit=btn.getAttribute('data-fp-open');
    if(explicit && explicit.trim()){
      const input=document.querySelector(explicit); if(input){ openFor(input); return; }
    }
    const scope=btn.closest('[data-fp-scope]')||btn.parentElement||document;
    let input=scope.querySelector('input[data-filepicker]');
    if(!input) input=document.querySelector('input[data-filepicker]');
    if(input) openFor(input);
  }));

  // Doble click en input
  $$('input[data-filepicker]').forEach(inp=>{
    inp.addEventListener('dblclick',()=>openFor(inp));
  });
})();
</script>
@endpush
