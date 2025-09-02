(() => {
  const $ = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  async function analyzeTechSEO(url){
    const res = await fetch('/api/techseo/analyze', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
      body: JSON.stringify({ url })
    });
    return await res.json();
  }

  function setScore(el, n){
    if (!el) return;
    const v = Math.max(0, Math.min(100, parseInt(n||0,10)));
    el.textContent = v + '/100';
    el.style.setProperty('--score', v);
    // Optional: if you have a progress bar element inside
    const bar = el.closest('[data-bar]')?.querySelector('[data-fill]');
    if (bar) bar.style.width = v + '%';
  }

  function list(el, arr){
    if (!el) return;
    el.innerHTML = (arr||[]).map(t => `<li>${escapeHtml(t)}</li>`).join('');
  }

  function tbl(el, rows){
    if (!el) return;
    el.innerHTML = rows.map(r => `
      <tr>
        <td><a href="${r.url}" target="_blank" rel="noopener">${escapeHtml(r.url)}</a></td>
        <td>${escapeHtml(r.anchor || r.reason || '')}</td>
      </tr>
    `).join('');
  }

  function imgs(el, items){
    if (!el) return;
    el.innerHTML = items.map(it => `
      <div class="img-row">
        <div class="src"><a href="${it.src}" target="_blank" rel="noopener">${it.src.split('/').pop()}</a></div>
        <div class="alt cur">${escapeHtml(it.current_alt || '(missing)')}</div>
        <div class="alt sug">${escapeHtml(it.suggested_alt || '')}</div>
      </div>
    `).join('');
  }

  function tree(el, data){
    if (!el) return;
    el.innerHTML = renderTree(data);
  }
  function renderTree(nodes){
    if (!nodes || !nodes.length) return '';
    return `<ul>` + nodes.map(n => `
      <li><span class="h${n.level}">H${n.level}: ${escapeHtml(n.text)}</span>
        ${renderTree(n.children)}
      </li>`).join('') + `</ul>`;
  }

  function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

  // Wire up your Analyze button (reuse your existing URL input)
  document.addEventListener('DOMContentLoaded', () => {
    const btn = document.querySelector('[data-techseo-analyze]');
    const input = document.querySelector('[data-url-input]');
    if (!btn || !input) return;

    btn.addEventListener('click', async () => {
      const url = input.value.trim();
      if (!url) return;
      btn.disabled = true;
      btn.dataset.loading = '1';

      try {
        const data = await analyzeTechSEO(url);
        if (!data.ok) throw new Error(data.error || 'Failed');

        // Scores
        setScore($('#score-url-structure'), data.scores.url_structure);
        setScore($('#score-meta'), data.scores.meta);
        setScore($('#score-images'), data.scores.images);
        setScore($('#score-internal'), data.scores.internal_links);
        setScore($('#score-structure'), data.scores.structure);
        setScore($('#score-overall-techseo'), data.scores.overall);

        // URL structure issues
        list($('#url-issues'), data.url_structure.issues);

        // Meta suggestions + AI
        $('#meta-title-current') && ($('#meta-title-current').textContent = data.meta.title || '(missing)');
        $('#meta-desc-current') && ($('#meta-desc-current').textContent = data.meta.description || '(missing)');
        if (data.meta.ai){
          $('#meta-title-suggested') && ($('#meta-title-suggested').textContent = data.meta.ai.title || '');
          $('#meta-desc-suggested') && ($('#meta-desc-suggested').textContent = data.meta.ai.description || '');
        }

        // Image alt
        imgs($('#images-table'), data.images.suggestions);

        // Internal links (heuristic + AI)
        tbl($('#internal-links-table'), (data.internal_linking.ai || data.internal_linking.candidates));

        // Structure tree
        tree($('#structure-tree'), data.structure.tree);

      } catch (e){
        console.error(e);
        alert('Technical SEO analyze failed.');
      } finally {
        btn.disabled = false;
        delete btn.dataset.loading;
      }
    });
  });
})();
