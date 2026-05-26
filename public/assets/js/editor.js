/**
 * IEF Framework — Canlı Editör (Inline Edit Layer)
 * Onur Sürücü Kursu birebir port.
 *
 * Text / HTML  → contenteditable + blur kaydet
 * Image / Video → modal ile yükle veya URL
 * Icon → Font Awesome galerisinden seç
 */
console.log('%c[osk-edit] editor.js YÜKLENDİ', 'background:#dc2626;color:#fff;padding:4px 10px;border-radius:4px;font-weight:bold');
(function () {
  'use strict';

  const SAVE_URL       = '/admin/editor/save';
  const UPLOAD_URL     = '/admin/editor/upload';
  const APPEARANCE_URL = '/site-editor/appearance/save';
  const log = (...a) => console.log('[osk-edit]', ...a);

  // ─── Toast ──────────────────────────────────────────────
  function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = 'osk-toast ' + type;
    el.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i> ${msg}`;
    document.body.appendChild(el);
    requestAnimationFrame(() => el.classList.add('show'));
    setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 250); }, 2200);
  }

  // ─── API ────────────────────────────────────────────────
  async function saveContent(key, value, type) {
    const fd = new FormData();
    fd.append('key', key);
    fd.append('value', value);
    fd.append('type', type);
    const r = await fetch(SAVE_URL, { method: 'POST', body: fd, credentials: 'same-origin' });
    const txt = await r.text();
    let data;
    try { data = JSON.parse(txt); } catch { throw new Error('Sunucu yanıtı JSON değil: ' + txt.slice(0,120)); }
    if (!r.ok || !data.ok) throw new Error(data.error || `HTTP ${r.status}`);
    return data;
  }

  async function uploadFile(file) {
    const fd = new FormData();
    fd.append('file', file);
    const r = await fetch(UPLOAD_URL, { method: 'POST', body: fd, credentials: 'same-origin' });
    const data = await r.json();
    if (!r.ok || !data.ok) throw new Error(data.error || 'upload failed');
    return data.url;
  }

  // ─── 2-Modlu sistem: Gezinme vs Düzenleme ────────────────
  let mode = 'edit';

  function setMode(newMode) {
    mode = newMode;
    document.body.classList.toggle('osk-mode-edit', mode === 'edit');
    document.body.classList.toggle('osk-mode-browse', mode === 'browse');
    document.querySelectorAll('[data-editable]').forEach(el => {
      const t = el.dataset.editableType;
      if (t === 'text' || t === 'html') {
        el.setAttribute('contenteditable', mode === 'edit' ? 'true' : 'false');
      }
    });
    updateModeButton();
    log('Mod:', mode);
  }

  function updateModeButton() {
    const btn = document.getElementById('oskModeToggle');
    if (!btn) return;
    if (mode === 'edit') {
      btn.innerHTML = '<i class="fa-solid fa-arrow-pointer"></i> Gezinme Moduna Geç';
    } else {
      btn.innerHTML = '<i class="fa-solid fa-pen"></i> Düzenleme Moduna Geç';
    }
  }

  function bindNavigationGuard() {
    // Session bazlı: link'lere ?edit=1 eklemiyoruz, sadece editable üstüne
    // tıklayınca link navigation'unu engelliyoruz (metin yazılabilsin diye).
    document.addEventListener('click', (e) => {
      const a = e.target.closest('a[href]');
      if (!a) return;
      if (a.closest('.osk-edit-bar')) return;

      if (mode === 'edit' && e.target.closest('[data-editable]')) {
        e.preventDefault();
        e.stopPropagation();
      }
    }, true);
  }

  function neutralizeBlockers() {
    const loader = document.getElementById('oskLoader');
    if (loader) { loader.classList.add('gone'); setTimeout(() => loader.remove(), 100); }
    document.body.classList.remove('osk-loading');

    document.querySelectorAll('[data-animate="fade-up"]').forEach(el => {
      el.style.opacity = '1';
      el.style.transform = 'none';
    });
    if (window.gsap) {
      try { window.gsap.set('[data-animate="fade-up"]', { opacity: 1, y: 0, clearProps: 'all' }); } catch {}
    }

    document.querySelectorAll('.whatsapp-float, [data-whatsapp]').forEach(el => {
      el.style.zIndex = '50';
    });
  }

  // ─── Auto-scanner ────────────────────────────────────────
  function pathOf(el) {
    const segs = [];
    while (el && el.nodeType === 1 && el.tagName.toLowerCase() !== 'main') {
      const parent = el.parentElement;
      if (!parent) break;
      const tag = el.tagName.toLowerCase();
      const siblings = Array.from(parent.children).filter(s => s.tagName === el.tagName);
      const idx = siblings.indexOf(el);
      segs.unshift(`${tag}[${idx}]`);
      el = parent;
    }
    return segs.join('/');
  }

  const INLINE_TAGS = new Set(['span','em','strong','b','i','u','small','sub','sup','br','a','code','mark']);

  function isInlineOnly(el) {
    for (const child of el.children) {
      if (!INLINE_TAGS.has(child.tagName.toLowerCase())) return false;
      if (!isInlineOnly(child)) return false;
    }
    return true;
  }

  function autoScanEditables() {
    const main = document.querySelector('main');
    if (!main) return 0;
    const pagePath = window.OSK_PAGE_PATH || 'home';
    let textCount = 0, imgCount = 0;

    const selector = 'h1,h2,h3,h4,h5,h6,p,li,blockquote,figcaption,button';
    main.querySelectorAll(selector).forEach(el => {
      if (el.hasAttribute('data-editable')) return;
      if (el.closest('[data-editable]') && el.closest('[data-editable]') !== el) return;
      if (el.closest('.osk-edit-bar, #oskLoader, [data-no-edit]')) return;
      const txt = el.textContent.trim();
      if (!txt) return;
      if (!isInlineOnly(el)) return;
      if (el.querySelector('[data-editable]')) return;

      const xpath = pathOf(el);
      const key = `auto.${pagePath}.${xpath}`;
      el.setAttribute('data-editable', key);
      el.setAttribute('data-editable-type', el.children.length > 0 ? 'html' : 'text');
      el.setAttribute('data-editable-key', key);
      textCount++;
    });

    main.querySelectorAll('img').forEach(img => {
      if (img.closest('[data-editable-type="image"]')) return;
      if (img.closest('.osk-edit-bar, #oskLoader, [data-no-edit]')) return;
      const rect = img.getBoundingClientRect();
      if (rect.width < 48 && rect.height < 48) return;
      if (!img.getAttribute('src')) return;

      const xpath = pathOf(img);
      const key = `auto.${pagePath}.${xpath}`;
      const wrapper = document.createElement('div');
      wrapper.style.cssText = 'display:inline-block;position:relative;line-height:0;' + (img.parentElement.tagName === 'A' ? '' : 'width:100%;height:100%');
      wrapper.className = 'osk-img-wrap';
      wrapper.setAttribute('data-editable', key);
      wrapper.setAttribute('data-editable-type', 'image');
      wrapper.setAttribute('data-editable-key', key);
      img.parentNode.insertBefore(wrapper, img);
      wrapper.appendChild(img);
      imgCount++;
    });

    log(`Auto-scan: ${textCount} text + ${imgCount} görsel düzenlenebilir.`);
    return textCount + imgCount;
  }

  // ─── TEXT / HTML editors ────────────────────────────────
  function initTextEditors() {
    const els = document.querySelectorAll('[data-editable]');
    let count = 0;
    els.forEach(el => {
      const type = el.dataset.editableType;
      if (type !== 'text' && type !== 'html') return;
      count++;

      el.setAttribute('spellcheck', 'true');

      const parentLink = el.closest('a[href]');
      if (parentLink) {
        el.addEventListener('mousedown', (e) => { if (mode === 'edit') e.stopPropagation(); });
        el.addEventListener('click', (e) => { if (mode === 'edit') { e.preventDefault(); e.stopPropagation(); el.focus(); } });
      }

      let original = el.innerHTML;
      el.addEventListener('focus', () => { original = el.innerHTML; });
      el.addEventListener('blur', async () => {
        const newVal = type === 'text' ? el.innerText.trim() : el.innerHTML.trim();
        const oldVal = type === 'text' ? (new DOMParser().parseFromString(original, 'text/html')).body.innerText.trim() : original.trim();
        if (newVal === oldVal) return;
        try {
          await saveContent(el.dataset.editableKey, newVal, type);
          toast('Kaydedildi ✓');
        } catch (err) {
          toast(err.message || 'Kaydedilemedi', 'error');
          el.innerHTML = original;
        }
      });
      el.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && type === 'text' && !e.shiftKey) {
          e.preventDefault();
          el.blur();
        }
        if (e.key === 'Escape') { el.innerHTML = original; el.blur(); }
      });
    });
    log(`${count} metin alanı düzenlenebilir.`);
  }

  // ─── IMAGE picker ───────────────────────────────────────
  function buildImageModal() {
    const m = document.createElement('div');
    m.className = 'osk-modal';
    m.id = 'oskImageModal';
    m.innerHTML = `
      <div class="osk-modal__panel">
        <div class="osk-modal__head">
          <h3><i class="fa-solid fa-image"></i> Görsel / Video Değiştir</h3>
          <button type="button" class="osk-modal__close" data-close>✕</button>
        </div>
        <div class="osk-modal__body">
          <label class="osk-image-picker__drop" id="oskDrop">
            <div class="icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
            <p style="font-weight:700;color:#0f172a;margin:0">Dosya seç veya buraya sürükle</p>
            <p style="font-size:12px;color:#94a3b8;margin-top:4px">JPG, PNG, WebP, GIF, SVG, MP4, WebM · max 20MB</p>
            <input type="file" id="oskFile" accept="image/*,video/*" style="display:none">
          </label>
          <div style="margin:20px 0 12px;display:flex;align-items:center;gap:12px;color:#94a3b8;font-size:12px">
            <span style="flex:1;height:1px;background:#e2e8f0"></span>
            <span>veya URL gir</span>
            <span style="flex:1;height:1px;background:#e2e8f0"></span>
          </div>
          <div style="display:flex;gap:8px">
            <input type="url" id="oskUrl" placeholder="https://... veya /assets/img/..."
              style="flex:1;height:44px;padding:0 14px;border:1px solid #cbd5e1;border-radius:10px;font:inherit;outline:none">
            <button type="button" id="oskUrlApply"
              style="height:44px;padding:0 18px;border-radius:10px;background:#dc2626;color:#fff;border:0;font:600 14px Inter;cursor:pointer">Uygula</button>
          </div>
        </div>
      </div>`;
    document.body.appendChild(m);
    return m;
  }

  let imgModal = null;
  let imgTargetEl = null;

  function openImageModal(targetEl) {
    if (!imgModal) imgModal = buildImageModal();
    imgTargetEl = targetEl;
    imgModal.classList.add('show');
  }

  function closeImageModal() {
    if (imgModal) imgModal.classList.remove('show');
    imgTargetEl = null;
  }

  async function applyImageUrl(url) {
    if (!imgTargetEl || !url) return;
    const key = imgTargetEl.dataset.editableKey;
    try {
      await saveContent(key, url, 'image');
      const img = imgTargetEl.querySelector('img');
      const isVideo = /\.(mp4|webm|mov)$/i.test(url);
      if (isVideo) {
        imgTargetEl.innerHTML = `<video src="${url}" autoplay muted loop playsinline class="${img ? img.className : ''}"></video>`;
      } else if (img) {
        img.src = url;
      } else {
        imgTargetEl.innerHTML = `<img src="${url}" alt="">`;
      }
      toast('Görsel güncellendi ✓');
      closeImageModal();
    } catch (e) {
      toast(e.message || 'Kayıt başarısız', 'error');
    }
  }

  function initImageEditors() {
    let count = 0;
    document.querySelectorAll('[data-editable-type="image"]').forEach(el => {
      count++;
      el.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        openImageModal(el);
      });
    });
    log(`${count} görsel düzenlenebilir.`);

    document.addEventListener('click', async (e) => {
      if (e.target.closest('[data-close]') && e.target.closest('#oskImageModal')) {
        closeImageModal(); return;
      }
      if (e.target.id === 'oskImageModal') { closeImageModal(); return; }
      if (e.target.id === 'oskUrlApply') {
        const url = document.getElementById('oskUrl').value.trim();
        if (url) applyImageUrl(url);
        return;
      }
    });

    document.addEventListener('change', async (e) => {
      if (e.target.id === 'oskFile') {
        const file = e.target.files[0];
        if (!file) return;
        toast('Yükleniyor…', 'success');
        try {
          const url = await uploadFile(file);
          await applyImageUrl(url);
        } catch (err) {
          toast(err.message || 'Yükleme hatası', 'error');
        }
      }
    });

    document.addEventListener('dragover', (e) => {
      if (e.target.closest('#oskDrop')) {
        e.preventDefault();
        e.target.closest('#oskDrop').classList.add('dragover');
      }
    });
    document.addEventListener('dragleave', (e) => {
      if (e.target.closest('#oskDrop')) e.target.closest('#oskDrop').classList.remove('dragover');
    });
    document.addEventListener('drop', async (e) => {
      const drop = e.target.closest('#oskDrop');
      if (!drop) return;
      e.preventDefault();
      drop.classList.remove('dragover');
      const file = e.dataTransfer.files[0];
      if (!file) return;
      try {
        const url = await uploadFile(file);
        await applyImageUrl(url);
      } catch (err) { toast(err.message, 'error'); }
    });
  }

  // ─── ICON picker ────────────────────────────────────────
  const FA_ICONS = [
    'fa-car','fa-car-side','fa-motorcycle','fa-truck','fa-bus','fa-bicycle','fa-tractor',
    'fa-id-card','fa-id-badge','fa-id-card-clip','fa-file-pen','fa-file-signature','fa-file-shield',
    'fa-graduation-cap','fa-school','fa-book-open','fa-book-open-reader','fa-clipboard-check','fa-clipboard-list',
    'fa-route','fa-road','fa-map','fa-map-location-dot','fa-location-dot','fa-compass',
    'fa-user','fa-user-tie','fa-user-graduate','fa-user-gear','fa-user-shield','fa-user-check','fa-users',
    'fa-shield','fa-shield-halved','fa-lock','fa-key',
    'fa-phone','fa-envelope','fa-comments','fa-headset','fa-paper-plane','fa-message',
    'fa-calendar','fa-calendar-check','fa-calendar-day','fa-calendar-days','fa-clock',
    'fa-bolt','fa-fire','fa-star','fa-heart','fa-thumbs-up','fa-trophy','fa-medal','fa-award',
    'fa-chart-line','fa-chart-bar','fa-chart-pie','fa-percent',
    'fa-circle-check','fa-circle-exclamation','fa-circle-info','fa-circle-question','fa-triangle-exclamation',
    'fa-arrow-right','fa-arrow-left','fa-arrow-up','fa-arrow-down','fa-arrow-up-right-from-square',
    'fa-cloud-arrow-up','fa-cloud-arrow-down','fa-download','fa-upload','fa-image','fa-camera','fa-video',
    'fa-folder','fa-folder-open','fa-file','fa-magnifying-glass','fa-eye','fa-gear','fa-sliders',
    'fa-house','fa-building','fa-shop','fa-store','fa-gas-pump','fa-traffic-light',
    'fa-venus','fa-mars','fa-venus-mars','fa-handshake','fa-language','fa-globe',
    'fa-stethoscope','fa-brain','fa-image-portrait',
  ];

  function buildIconModal() {
    const m = document.createElement('div');
    m.className = 'osk-modal';
    m.id = 'oskIconModal';
    m.innerHTML = `
      <div class="osk-modal__panel">
        <div class="osk-modal__head">
          <h3><i class="fa-solid fa-icons"></i> İkon Seç</h3>
          <button type="button" class="osk-modal__close" data-close>✕</button>
        </div>
        <div class="osk-modal__body">
          <input type="text" class="osk-icon-picker__search" id="oskIconSearch" placeholder="İkon ara... (örn: car, user, star)">
          <div class="osk-icon-picker__grid" id="oskIconGrid"></div>
        </div>
      </div>`;
    document.body.appendChild(m);
    return m;
  }

  let iconModal = null;
  let iconTargetEl = null;

  function renderIconGrid(filter = '') {
    const grid = document.getElementById('oskIconGrid');
    if (!grid) return;
    const items = FA_ICONS.filter(i => i.includes(filter.toLowerCase()));
    grid.innerHTML = items.map(i => `<div class="osk-icon-picker__item" data-icon="fa-solid ${i}"><i class="fa-solid ${i}"></i></div>`).join('');
  }

  function openIconModal(targetEl) {
    if (!iconModal) iconModal = buildIconModal();
    iconTargetEl = targetEl;
    iconModal.classList.add('show');
    renderIconGrid('');
    setTimeout(() => document.getElementById('oskIconSearch')?.focus(), 100);
  }

  function closeIconModal() {
    if (iconModal) iconModal.classList.remove('show');
    iconTargetEl = null;
  }

  async function applyIcon(faClass) {
    if (!iconTargetEl) return;
    try {
      await saveContent(iconTargetEl.dataset.editableKey, faClass, 'icon');
      const i = iconTargetEl.querySelector('i');
      if (i) i.className = faClass;
      toast('İkon güncellendi ✓');
      closeIconModal();
    } catch (e) {
      toast(e.message || 'Kayıt başarısız', 'error');
    }
  }

  function initIconEditors() {
    let count = 0;
    document.querySelectorAll('[data-editable-type="icon"]').forEach(el => {
      count++;
      el.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        openIconModal(el);
      });
    });
    log(`${count} ikon düzenlenebilir.`);

    document.addEventListener('click', (e) => {
      if (e.target.closest('[data-close]') && e.target.closest('#oskIconModal')) { closeIconModal(); return; }
      if (e.target.id === 'oskIconModal') { closeIconModal(); return; }
      const item = e.target.closest('.osk-icon-picker__item');
      if (item) applyIcon(item.dataset.icon);
    });
    document.addEventListener('input', (e) => {
      if (e.target.id === 'oskIconSearch') renderIconGrid(e.target.value);
    });
  }

  // ─── Tema/Görünüm Sidebar ───────────────────────────────
  const FONTS = ['Poppins','Inter','Roboto','Open Sans','Manrope','Plus Jakarta Sans','Nunito','Lato','Montserrat','Raleway'];

  async function saveAppearance(key, value) {
    const fd = new FormData();
    fd.append('key', key);
    fd.append('value', value);
    const r = await fetch(APPEARANCE_URL, { method: 'POST', body: fd, credentials: 'same-origin' });
    const data = await r.json();
    if (!r.ok || !data.ok) throw new Error(data.error || `HTTP ${r.status}`);
    return data;
  }

  // debounce per-key
  const _debouncers = {};
  function debounceSave(key, value, delay = 400) {
    clearTimeout(_debouncers[key]);
    _debouncers[key] = setTimeout(async () => {
      try {
        await saveAppearance(key, value);
        flashSaved(key);
      } catch (e) {
        toast(e.message || 'Kayıt başarısız', 'error');
      }
    }, delay);
  }

  function flashSaved(key) {
    const ind = document.querySelector(`[data-saved-for="${key}"]`);
    if (!ind) return;
    ind.classList.add('show');
    setTimeout(() => ind.classList.remove('show'), 1200);
  }

  function buildUploadField(key, label, hint, currentValue) {
    const hasVal = currentValue && currentValue.length > 0;
    const preview = hasVal
      ? `<img src="${currentValue}" class="preview" alt="">`
      : '';
    return `
      <div class="osk-field">
        <label>${label}</label>
        <label class="osk-upload ${hasVal ? 'has-file' : ''}" data-upload-key="${key}">
          ${preview}
          ${!hasVal ? '<i class="fa-solid fa-cloud-arrow-up"></i>' : ''}
          <span class="label">${hasVal ? 'Değiştir' : 'Dosya seç veya sürükle'}</span>
          <span class="hint">${hint}</span>
          <input type="file" accept="image/*">
          <button type="button" class="clear" title="Kaldır"><i class="fa-solid fa-xmark"></i></button>
        </label>
        <input type="text" data-text-for="${key}" value="${currentValue || ''}" placeholder="veya URL yapıştır" style="margin-top:6px">
        <span class="osk-save-indicator" data-saved-for="${key}"><i class="fa-solid fa-check"></i> kaydedildi</span>
      </div>`;
  }

  function buildSidebar() {
    const ap = window.OSK_APPEARANCE || {};
    const sb = document.createElement('aside');
    sb.className = 'osk-sidebar';
    sb.id = 'oskSidebar';
    sb.innerHTML = `
      <div class="osk-sidebar__head">
        <h3><i class="fa-solid fa-palette"></i> Tema & Görünüm</h3>
        <button type="button" class="osk-sidebar__close" id="oskSidebarClose"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="osk-sidebar__body">

        <div class="osk-sidebar__section">
          <h4>Logo & Görseller</h4>
          ${buildUploadField('appearance.logo_url',        'Logo (açık tema)',     'PNG/SVG · max 5MB',  ap.logo_url || '')}
          ${buildUploadField('appearance.logo_dark_url',   'Logo (koyu tema)',     'PNG/SVG · max 5MB',  ap.logo_dark_url || '')}
          ${buildUploadField('appearance.favicon_url',     'Favicon',              'ICO/PNG · 32x32+',   ap.favicon_url || '')}
          ${buildUploadField('appearance.og_default_url',  'Varsayılan OG Görsel', 'JPG/PNG · 1200x630', ap.og_default_url || '')}
        </div>

        <div class="osk-sidebar__section">
          <h4>Renk Paleti</h4>
          <div class="osk-field">
            <label>Primary</label>
            <div class="osk-color">
              <input type="color" data-color-key="appearance.primary_color" value="${ap.primary_color || '#1d4ed8'}">
              <input type="text"  data-color-text="appearance.primary_color" value="${ap.primary_color || '#1d4ed8'}">
            </div>
            <span class="osk-save-indicator" data-saved-for="appearance.primary_color"><i class="fa-solid fa-check"></i> kaydedildi</span>
          </div>
          <div class="osk-field">
            <label>Accent</label>
            <div class="osk-color">
              <input type="color" data-color-key="appearance.accent_color" value="${ap.accent_color || '#06b6d4'}">
              <input type="text"  data-color-text="appearance.accent_color" value="${ap.accent_color || '#06b6d4'}">
            </div>
            <span class="osk-save-indicator" data-saved-for="appearance.accent_color"><i class="fa-solid fa-check"></i> kaydedildi</span>
          </div>
        </div>

        <div class="osk-sidebar__section">
          <h4>Tipografi</h4>
          <div class="osk-field">
            <label>Font Ailesi</label>
            <select data-appearance-key="appearance.font_family">
              ${FONTS.map(f => `<option value="${f}" ${ (ap.font_family||'Poppins') === f ? 'selected' : '' }>${f}</option>`).join('')}
            </select>
            <span class="hint">Google Fonts üzerinden otomatik yüklenir.</span>
            <span class="osk-save-indicator" data-saved-for="appearance.font_family"><i class="fa-solid fa-check"></i> kaydedildi</span>
          </div>
        </div>

        <div class="osk-sidebar__section">
          <h4>Footer Metinleri</h4>
          <div class="osk-field">
            <label>Tanıtım Metni</label>
            <textarea data-appearance-key="appearance.footer_text" rows="3" placeholder="Şirket hakkında kısa açıklama...">${ap.footer_text || ''}</textarea>
            <span class="osk-save-indicator" data-saved-for="appearance.footer_text"><i class="fa-solid fa-check"></i> kaydedildi</span>
          </div>
          <div class="osk-field">
            <label>Telif Hakkı</label>
            <input type="text" data-appearance-key="appearance.copyright_text" value="${ap.copyright_text || ''}" placeholder="© {year} {site_name} · Tüm hakları saklıdır">
            <span class="hint"><code>{year}</code> ve <code>{site_name}</code> placeholder'ları desteklenir.</span>
            <span class="osk-save-indicator" data-saved-for="appearance.copyright_text"><i class="fa-solid fa-check"></i> kaydedildi</span>
          </div>
        </div>

      </div>`;
    document.body.appendChild(sb);
    bindSidebar();
    return sb;
  }

  function bindSidebar() {
    // Close
    document.getElementById('oskSidebarClose')?.addEventListener('click', () => {
      document.getElementById('oskSidebar')?.classList.remove('open');
    });

    // Color picker: sync color <-> text + save
    document.querySelectorAll('[data-color-key]').forEach(picker => {
      const key  = picker.dataset.colorKey;
      const text = document.querySelector(`[data-color-text="${key}"]`);
      picker.addEventListener('input', e => {
        if (text) text.value = e.target.value;
        debounceSave(key, e.target.value, 200);
      });
      text?.addEventListener('input', e => {
        const v = e.target.value.trim();
        if (/^#[0-9a-f]{6}$/i.test(v)) {
          picker.value = v;
          debounceSave(key, v, 400);
        }
      });
    });

    // Generic text/textarea/select inputs
    document.querySelectorAll('[data-appearance-key]').forEach(el => {
      const key = el.dataset.appearanceKey;
      const ev = el.tagName === 'SELECT' ? 'change' : 'input';
      el.addEventListener(ev, e => debounceSave(key, e.target.value, ev === 'change' ? 0 : 500));
    });

    // URL text input for uploads (eşli)
    document.querySelectorAll('[data-text-for]').forEach(input => {
      const key = input.dataset.textFor;
      input.addEventListener('input', e => {
        debounceSave(key, e.target.value, 500);
        // Preview güncelle
        const drop = document.querySelector(`[data-upload-key="${key}"]`);
        if (drop && e.target.value) updateUploadPreview(drop, e.target.value);
      });
    });

    // File upload (click + change + drag)
    document.querySelectorAll('[data-upload-key]').forEach(drop => {
      const key = drop.dataset.uploadKey;
      const fileInput = drop.querySelector('input[type="file"]');
      const textInput = document.querySelector(`[data-text-for="${key}"]`);
      const clearBtn  = drop.querySelector('.clear');

      fileInput.addEventListener('change', async () => {
        const file = fileInput.files[0];
        if (!file) return;
        await handleUpload(key, file, drop, textInput);
        fileInput.value = '';
      });

      drop.addEventListener('dragover', e => { e.preventDefault(); drop.classList.add('dragover'); });
      drop.addEventListener('dragleave', () => drop.classList.remove('dragover'));
      drop.addEventListener('drop', async e => {
        e.preventDefault();
        drop.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (!file) return;
        await handleUpload(key, file, drop, textInput);
      });

      clearBtn?.addEventListener('click', async e => {
        e.preventDefault();
        e.stopPropagation();
        if (textInput) textInput.value = '';
        drop.classList.remove('has-file');
        drop.querySelector('.preview')?.remove();
        try { await saveAppearance(key, ''); flashSaved(key); }
        catch (err) { toast(err.message, 'error'); }
      });
    });
  }

  async function handleUpload(key, file, drop, textInput) {
    try {
      toast('Yükleniyor…', 'success');
      const url = await uploadFile(file);
      if (textInput) textInput.value = url;
      updateUploadPreview(drop, url);
      await saveAppearance(key, url);
      flashSaved(key);
      toast('Yüklendi ✓');
    } catch (err) {
      toast(err.message || 'Yükleme hatası', 'error');
    }
  }

  function updateUploadPreview(drop, url) {
    drop.classList.add('has-file');
    let img = drop.querySelector('.preview');
    if (!img) {
      img = document.createElement('img');
      img.className = 'preview';
      img.alt = '';
      drop.prepend(img);
    }
    img.src = url;
    // Kutuyu sadeleştir
    drop.querySelector('.osk-upload > i')?.remove();
    const label = drop.querySelector('.label');
    if (label) label.textContent = 'Değiştir';
  }

  // ─── Bootstrap ──────────────────────────────────────────
  function init() {
    log('Editör başlatılıyor…');
    document.body.classList.add('osk-editing');
    neutralizeBlockers();
    bindNavigationGuard();
    autoScanEditables();
    initTextEditors();
    initImageEditors();
    initIconEditors();

    const pageEditable = window.OSK_PAGE_EDITABLE === true;
    const bar = document.createElement('div');
    bar.className = 'osk-edit-bar';
    const hint = pageEditable
      ? 'Düzenleme: metne tıkla yaz · Gezinme: linklere tıkla sayfayı dolaş'
      : 'Bu sayfada sadece <strong>header/footer</strong> düzenlenir · Ana sayfa için sol üstten dön';
    bar.innerHTML = `
      <span class="osk-edit-bar__title"><i class="fa-solid fa-pen-to-square"></i> Editör</span>
      <button type="button" id="oskModeToggle" class="osk-edit-bar__toggle"></button>
      <span class="osk-edit-bar__hint">${hint}</span>
      <button type="button" id="oskThemeToggle"><i class="fa-solid fa-palette"></i> Tema</button>
      <a href="/">← Ana Sayfa</a>
      <a href="/admin">Admin</a>
      <a href="/site-editor/cikis">Kapat</a>
    `;
    document.body.appendChild(bar);

    document.getElementById('oskModeToggle').addEventListener('click', () => {
      setMode(mode === 'edit' ? 'browse' : 'edit');
    });
    setMode('edit');

    // Tema sidebar
    buildSidebar();
    document.getElementById('oskThemeToggle').addEventListener('click', () => {
      document.getElementById('oskSidebar').classList.toggle('open');
    });

    log('Editör hazır ✓');
    toast(pageEditable ? 'Düzenleme modu aktif' : 'Header/footer düzenlenebilir · Ana sayfa için sol üstten dön', 'success');
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
