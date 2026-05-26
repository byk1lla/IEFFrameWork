/**
 * ief-framework · Auto-override applier
 * Her ziyaretçi için: DB'deki auto-scanned düzenleme override'larını DOM'a uygular.
 * Edit modunda DEĞİL — sadece "düzenlenmiş içeriği güncel göstermek" için.
 */
(function () {
    if (window.IEF_IS_EDITING) return; // edit modunda zaten editör hallediyor
    const ov = window.IEF_AUTO_OVERRIDES || {};
    if (!Object.keys(ov).length) return;

    function apply() {
        const path = window.IEF_PAGE_PATH || 'home';
        const main = document.querySelector('main') || document.body;
        const counters = {};

        ['h1','h2','h3','h4','h5','h6','p','li','blockquote','button','span','a'].forEach(tag => {
            main.querySelectorAll(tag).forEach(el => {
                if (el.closest('.ief-edit-bar, nav#navTop, footer')) return;
                if (!el.textContent.trim()) return;
                if (el.children.length && !['span','a'].includes(tag)) return;
                const i = counters[tag] = (counters[tag] || 0) + 1;
                const k = `${tag}_${i}`;
                if (ov[k] && ov[k].v != null && ov[k].t !== 'image') {
                    el.textContent = ov[k].v;
                }
            });
        });

        // Image overrides
        let imgIdx = 0;
        main.querySelectorAll('img').forEach(img => {
            if (img.closest('.ief-edit-bar, nav#navTop, footer')) return;
            imgIdx++;
            const k = `img_${imgIdx}`;
            if (ov[k] && ov[k].v) img.src = ov[k].v;
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', apply);
    } else apply();
})();
