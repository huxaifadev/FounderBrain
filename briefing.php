<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Executive Insights — FounderBrain';
$st = db()->prepare('SELECT * FROM briefings WHERE user_id=? ORDER BY created_at DESC LIMIT 1');
$st->execute([$_SESSION['user_id']]); $last = $st->fetch();
$tc = db()->prepare('SELECT COUNT(*) FROM tasks WHERE user_id=? AND done=0');
$tc->execute([$_SESSION['user_id']]); $tcount = $tc->fetchColumn();
include __DIR__ . '/includes/header.php';
?>
<div class="topbar">
  <div class="topbar-breadcrumb">
    <span class="crumb-main">Daily Briefing</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-sub">Morning Concierge</span>
  </div>
  <div class="topbar-search" style="margin-left:auto;">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--ink4)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input placeholder="Search strategy..."/>
  </div>
</div>

<div class="page">
  <div style="display:grid;grid-template-columns:420px 1fr;gap:22px;align-items:flex-start;">

    <!-- LEFT: Form -->
    <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:24px 26px;">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
        <div style="width:32px;height:32px;background:var(--indigobg);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--indigo);">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        </div>
        <div style="font-size:16px;font-weight:700;color:var(--ink);">Tell me your situation</div>
      </div>

      <div style="font-size:10.5px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Startup Stage</div>
      <select id="stage" style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius);padding:10px 13px;font-family:Inter,sans-serif;font-size:13px;color:var(--ink);background:var(--white);outline:none;margin-bottom:14px;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b6b85' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center;padding-right:32px;cursor:pointer;">
        <option>Pre-seed / Ideation</option>
        <option>Seed / Early Traction</option>
        <option selected>Series A / Growth</option>
        <option>Series B / Scale</option>
      </select>

      <div style="font-size:10.5px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Working on This Week</div>
      <textarea id="week" rows="3" style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius);padding:10px 13px;font-family:Inter,sans-serif;font-size:13px;color:var(--ink);background:var(--white);outline:none;resize:vertical;line-height:1.6;margin-bottom:14px;transition:border-color .12s;" onfocus="this.style.borderColor='var(--indigo)'" onblur="this.style.borderColor='var(--border)'" placeholder="Key initiatives and focus areas..."></textarea>

      <div style="font-size:10.5px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Stressed About / Stuck</div>
      <textarea id="stress" rows="3" style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius);padding:10px 13px;font-family:Inter,sans-serif;font-size:13px;color:var(--ink);background:var(--white);outline:none;resize:vertical;line-height:1.6;margin-bottom:14px;transition:border-color .12s;" onfocus="this.style.borderColor='var(--indigo)'" onblur="this.style.borderColor='var(--border)'" placeholder="What's keeping you up at night?"></textarea>

      <div style="font-size:10.5px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px;">Deadlines This Week</div>
      <input id="deadlines" style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius);padding:10px 13px;font-family:Inter,sans-serif;font-size:13px;color:var(--ink);background:var(--white);outline:none;margin-bottom:16px;transition:border-color .12s;" onfocus="this.style.borderColor='var(--indigo)'" onblur="this.style.borderColor='var(--border)'" placeholder="Investor pitch, product launch..."/>

      <button onclick="generate()" id="genBtn" style="width:100%;background:var(--indigo);color:white;border:none;border-radius:var(--radius);padding:13px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .12s;font-family:Inter,sans-serif;">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        Generate My Briefing
      </button>
    </div>

    <!-- RIGHT: Result -->
    <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;min-height:400px;" id="briefResult">
      <?php if ($last): ?>
      <div style="background:var(--indigo);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;">
        <span style="font-size:14px;font-weight:700;color:white;">☀️ Morning Briefing</span>
        <span style="font-size:12px;color:rgba(255,255,255,.7);"><?= date('M j, Y', strtotime($last['created_at'])) ?></span>
      </div>
      <div style="padding:20px 22px;font-size:13.5px;color:var(--ink2);line-height:1.8;"><?= nl2br(htmlspecialchars($last['content'])) ?></div>
      <?php else: ?>
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 28px;text-align:center;height:100%;">
        <div style="width:56px;height:56px;background:var(--indigobg);border-radius:14px;display:flex;align-items:center;justify-content:center;color:var(--indigo);margin:0 auto 18px;">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <div style="font-size:20px;font-weight:800;color:var(--ink);letter-spacing:-.3px;margin-bottom:10px;">Your daily briefing is ready to be distilled.</div>
        <div style="font-size:13.5px;color:var(--ink3);line-height:1.7;max-width:300px;">Submit your current situation on the left. I'll analyze and return your top priorities, what you're missing, and one metric to watch.</div>
        <div style="margin-top:18px;background:var(--indigobg);border-radius:var(--radius);padding:12px 16px;font-size:12.5px;color:var(--ink2);line-height:1.6;max-width:320px;text-align:left;">
          <strong style="color:var(--indigo);">Pro Tip:</strong> Being specific about what "stuck" looks like allows me to search your history for similar patterns.
        </div>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<script>
async function generate() {
  const week = document.getElementById('week').value.trim();
  if (!week) { alert('Fill in what you are working on this week.'); return; }
  const btn = document.getElementById('genBtn'); btn.disabled=true; btn.textContent='Generating...';
  document.getElementById('briefResult').innerHTML='<div style="display:flex;gap:6px;align-items:center;justify-content:center;padding:80px 0;"><span style="width:7px;height:7px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s infinite;display:inline-block;"></span><span style="width:7px;height:7px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s .2s infinite;display:inline-block;"></span><span style="width:7px;height:7px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s .4s infinite;display:inline-block;"></span></div>';
  const ctx=`Stage: ${document.getElementById('stage').value}\nWorking on: ${week}\nStressed: ${document.getElementById('stress').value||'nothing'}\nDeadlines: ${document.getElementById('deadlines').value||'none'}\nOpen tasks: <?= $tcount ?>\nDate: <?= date('l, F j, Y') ?>`;
  const res=await fetch('api/chat.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({mode:'briefing',messages:[{role:'user',content:ctx}]})});
  const data=await res.json(); const text=data.reply||'';
  await fetch('api/briefing.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({content:text})});
  const fmt=text.replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>').replace(/\n/g,'<br>');
  document.getElementById('briefResult').innerHTML=`<div style="background:var(--indigo);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;"><span style="font-size:14px;font-weight:700;color:white;">☀️ Morning Briefing</span><span style="font-size:12px;color:rgba(255,255,255,.7);"><?= date('M j, Y') ?></span></div><div style="padding:20px 22px;font-size:13.5px;color:var(--ink2);line-height:1.8;">${fmt}</div>`;
  btn.disabled=false; btn.innerHTML='<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg> Generate My Briefing';
}
</script>
<style>@keyframes bounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1);opacity:1}}</style>
<?php include __DIR__ . '/includes/footer.php'; ?>
