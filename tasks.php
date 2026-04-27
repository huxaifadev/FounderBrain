<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Open Loops — FounderBrain';
$st = db()->prepare('SELECT * FROM tasks WHERE user_id=? AND done=0 ORDER BY FIELD(priority,"high","medium","low"),created_at DESC');
$st->execute([$_SESSION['user_id']]); $tasks = $st->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="topbar">
  <span style="font-size:14px;font-weight:700;color:var(--ink);">Open Loops</span>
  <span style="background:var(--indigobg);color:var(--indigo);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;margin-left:10px;">ACTIVE CAPTURE</span>
  <div class="topbar-search" style="margin-left:auto;">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--ink4)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input placeholder="Search insights..."/>
  </div>
</div>

<div class="page">
  <div style="display:grid;grid-template-columns:1fr 380px;gap:22px;align-items:flex-start;">

    <!-- LEFT: Chaos Capture -->
    <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:28px;">
      <div style="font-size:22px;font-weight:800;color:var(--ink);letter-spacing:-.4px;margin-bottom:6px;">Chaos Capture</div>
      <div style="font-size:13.5px;color:var(--ink3);margin-bottom:18px;line-height:1.6;">Pour every thought, task, and loose end here. AI will distill them into actionable loops.</div>

      <textarea id="dump" style="width:100%;min-height:220px;resize:none;border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:16px;font-family:Inter,sans-serif;font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;line-height:1.7;transition:border-color .12s;" onfocus="this.style.borderColor='var(--indigo)';this.style.background='white'" onblur="this.style.borderColor='var(--border)';this.style.background='var(--bg)'" placeholder="Need to follow up with the series A investors... also, the landing page copy for the new feature needs a refresh before Friday. Mention the Sequoia intro to Sarah... oh, and book flights for the NYC conference next month."></textarea>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:14px;">
        <div style="display:flex;gap:7px;flex-wrap:wrap;">
          <span style="font-size:11px;font-weight:600;color:var(--ink4);text-transform:uppercase;letter-spacing:.07em;margin-right:2px;">Suggestions</span>
          <span style="background:var(--bg);border:1px solid var(--border);border-radius:20px;padding:4px 11px;font-size:12px;color:var(--ink3);">#InvestorUpdates</span>
          <span style="background:var(--bg);border:1px solid var(--border);border-radius:20px;padding:4px 11px;font-size:12px;color:var(--ink3);">#ProductRoadmap</span>
          <span style="background:var(--bg);border:1px solid var(--border);border-radius:20px;padding:4px 11px;font-size:12px;color:var(--ink3);">#Hiring</span>
        </div>
        <button onclick="extract()" id="extractBtn" style="background:var(--indigo);color:white;border:none;border-radius:24px;padding:11px 24px;font-size:13.5px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:7px;transition:all .15s;font-family:Inter,sans-serif;white-space:nowrap;">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
          Extract Open Loops
        </button>
      </div>
    </div>

    <!-- RIGHT: Extracted loops -->
    <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;">
      <div style="padding:16px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:14px;font-weight:700;color:var(--ink);">Extracted Loops</span>
        <span id="activeCount" style="background:var(--indigobg);color:var(--indigo);font-size:11px;font-weight:700;padding:2px 10px;border-radius:20px;"><?= count($tasks) ?> Active</span>
      </div>

      <div id="loopsContent">
        <?php if (!$tasks): ?>
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 20px;text-align:center;">
          <div style="width:40px;height:40px;border-radius:50%;background:var(--bg);display:flex;align-items:center;justify-content:center;margin-bottom:12px;">
            <svg width="18" height="18" fill="none" stroke="var(--ink3)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          </div>
          <div style="font-size:13px;color:var(--ink3);line-height:1.6;">No loops yet.<br>Brain dump on the left<br>to reveal hidden tasks.</div>
        </div>
        <?php else: ?>
        <?php
        $priColors = ['high' => 'var(--red)', 'medium' => 'var(--amber)', 'low' => 'var(--green)'];
        foreach ($tasks as $t):
          $due = $t['deadline'] === 'today' ? 'Due: ASAP' : ($t['deadline'] === 'this-week' ? 'This week' : 'No deadline');
        ?>
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);" id="li-<?= $t['id'] ?>">
          <div style="font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:<?= $priColors[$t['priority']] ?? 'var(--ink4)' ?>;margin-bottom:5px;">Priority: <?= strtoupper($t['priority']) ?></div>
          <div style="font-size:14px;font-weight:700;color:var(--ink);margin-bottom:7px;"><?= htmlspecialchars($t['title']) ?></div>
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:5px;font-size:11.5px;color:var(--ink3);">
              <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <?= htmlspecialchars($due) ?>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
              <span style="font-size:11.5px;font-weight:600;color:var(--indigo);"><?= $t['priority'] === 'high' ? 'Actionable' : 'Pending' ?> ›</span>
              <button onclick="done(<?= $t['id'] ?>)" title="Mark done" style="background:none;border:none;color:var(--ink3);cursor:pointer;font-size:14px;padding:0;line-height:1;">✓</button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div onclick="document.getElementById('dump').focus()" style="padding:13px 18px;display:flex;align-items:center;justify-content:center;gap:7px;cursor:pointer;color:var(--ink3);font-size:13px;border:2px dashed var(--border);margin:14px;border-radius:var(--radius);transition:all .12s;" onmouseover="this.style.borderColor='var(--indigo)';this.style.color='var(--indigo)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--ink3)'">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        Add Loop Manually
      </div>
    </div>
  </div>
</div>

<script>
const priColors = {high:'var(--red)',medium:'var(--amber)',low:'var(--green)'};

async function extract() {
  const dump = document.getElementById('dump').value.trim();
  if (!dump) { alert('Pour something into the Chaos Capture first.'); return; }
  const btn = document.getElementById('extractBtn');
  btn.disabled = true; btn.innerHTML = '⏳ Extracting...';
  document.getElementById('loopsContent').innerHTML = '<div style="display:flex;gap:6px;align-items:center;justify-content:center;padding:40px;"><span style="width:7px;height:7px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s infinite;display:inline-block;"></span><span style="width:7px;height:7px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s .2s infinite;display:inline-block;"></span><span style="width:7px;height:7px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s .4s infinite;display:inline-block;"></span></div>';

  const res  = await fetch('api/chat.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({mode:'tasks', messages:[{role:'user', content:dump}]})});
  const data = await res.json();
  let tasks;
  try { tasks = JSON.parse(data.reply.replace(/```json|```/g,'').trim()); }
  catch(e) { document.getElementById('loopsContent').innerHTML=`<div style="padding:16px;color:var(--red);font-size:13px;">Parse error. Try again.</div>`; btn.disabled=false; btn.innerHTML='<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg> Extract Open Loops'; return; }

  await fetch('api/tasks.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'save', tasks})});
  document.getElementById('activeCount').textContent = tasks.length + ' Active';

  const sorted = tasks.sort((a,b)=>({high:0,medium:1,low:2}[a.priority]||1)-({high:0,medium:1,low:2}[b.priority]||1));
  document.getElementById('loopsContent').innerHTML = sorted.map(t=>`
    <div style="padding:14px 18px;border-bottom:1px solid var(--border);">
      <div style="font-size:10px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:${priColors[t.priority]||'var(--ink4)'};margin-bottom:5px;">Priority: ${t.priority.toUpperCase()}</div>
      <div style="font-size:14px;font-weight:700;color:var(--ink);margin-bottom:7px;">${esc(t.title)}</div>
      <div style="display:flex;align-items:center;justify-content:space-between;">
        <div style="font-size:11.5px;color:var(--ink3);">${t.deadline}</div>
        <span style="font-size:11.5px;font-weight:600;color:var(--indigo);">${t.priority==='high'?'Actionable':'Pending'} ›</span>
      </div>
    </div>`).join('') + `<div onclick="document.getElementById('dump').focus()" style="padding:13px 18px;display:flex;align-items:center;justify-content:center;gap:7px;cursor:pointer;color:var(--ink3);font-size:13px;border:2px dashed var(--border);margin:14px;border-radius:8px;">+ Add Loop Manually</div>`;

  btn.disabled = false;
  btn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg> Extract Open Loops';
}

async function done(id) {
  await fetch('api/tasks.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'done', id})});
  const el = document.getElementById('li-'+id);
  if (el) { el.style.opacity='0'; el.style.transition='.3s'; setTimeout(()=>el.remove(), 300); }
}

function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
</script>
<style>@keyframes bounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1);opacity:1}}</style>
<?php include __DIR__ . '/includes/footer.php'; ?>
