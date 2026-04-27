// FounderBrain — app.js
// Global utilities loaded on every page

// ── Auto-resize textareas ────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('textarea').forEach(function(ta) {
    ta.addEventListener('input', function() {
      this.style.height = 'auto';
      this.style.height = Math.min(this.scrollHeight, 220) + 'px';
    });
  });
});

// ── Toast notification ───────────────────────────────────
function showToast(msg, type) {
  type = type || 'info';
  var colors = {info:'var(--indigo)', success:'var(--green)', warn:'var(--amber)', error:'var(--red)'};
  var el = document.createElement('div');
  el.style.cssText = [
    'position:fixed', 'bottom:24px', 'right:24px', 'z-index:9999',
    'background:var(--white)', 'border:1px solid var(--border)',
    'border-left:3px solid ' + (colors[type] || colors.info),
    'border-radius:var(--radius)', 'padding:12px 16px',
    'font-size:13px', 'font-family:Inter,sans-serif', 'color:var(--ink)',
    'box-shadow:0 4px 20px rgba(0,0,0,.08)', 'max-width:320px',
    'animation:fadeUp .2s ease'
  ].join(';');
  el.textContent = msg;
  document.body.appendChild(el);
  setTimeout(function() { el.remove(); }, 4000);
}

// ── Global escape helper ─────────────────────────────────
function escHtml(s) {
  return String(s || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');
}

// ── Bounce animation (shared) ────────────────────────────
var style = document.createElement('style');
style.textContent = [
  '@keyframes bounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1);opacity:1}}',
  '@keyframes fadeUp{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:none}}',
  '@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}'
].join('');
document.head.appendChild(style);
