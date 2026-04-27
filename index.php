<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Dashboard — FounderBrain';
$user  = currentUser();
$fname = explode(' ', $user['name'] ?? 'Huzaifa')[0];
$h     = (int)date('H');
$gr    = $h < 12 ? 'Good morning' : ($h < 17 ? 'Good afternoon' : 'Good evening');
$tc    = db()->prepare('SELECT COUNT(*) FROM tasks WHERE user_id=? AND done=0');
$tc->execute([$_SESSION['user_id']]); $tasks = $tc->fetchColumn();
$gok   = hasGoogleAuth();
include __DIR__ . '/includes/header.php';
?>
<div class="topbar">
  <span style="font-size:14px;font-weight:700;color:var(--ink);">Dashboard</span>
  <div class="topbar-search" style="margin-left:auto;">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--ink4)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input placeholder="Search analytics..." onkeydown="if(event.key==='Enter')window.location='chat.php?q='+encodeURIComponent(this.value)"/>
  </div>
  <div class="topbar-actions">
    <div class="tb-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
    <a href="briefing.php" class="btn btn-indigo btn-sm">New Briefing</a>
  </div>
</div>

<div class="page">
  <!-- HERO -->
  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-xl);padding:28px 32px;margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:7px;font-size:11px;font-weight:700;color:var(--indigo);letter-spacing:.07em;text-transform:uppercase;margin-bottom:12px;">
      <span style="width:7px;height:7px;border-radius:50%;background:var(--indigo);animation:pulse 2s infinite;display:inline-block;"></span>
      Active Assistant
    </div>
    <div style="font-size:30px;font-weight:800;color:var(--ink);letter-spacing:-.6px;line-height:1.2;margin-bottom:12px;">
      <?= $gr ?>, <?= htmlspecialchars($fname) ?>.<br>I've triaged your world.
    </div>
    <div style="font-size:14px;color:var(--ink2);line-height:1.75;max-width:600px;margin-bottom:18px;">
      Welcome to your Command Center. You have
      <a href="tasks.php" style="color:var(--indigo);font-weight:600;text-decoration:none;"><?= $tasks ?> open loop<?= $tasks != 1 ? 's' : '' ?></a>
      requiring your decision<?= $gok ? ', your inbox has been categorized into actionable blocks' : '' ?>.
      Where should we focus first?
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <span class="chat-chip" onclick="window.location='chat.php?q=Summarize+my+week+and+tell+me+what+to+prioritize'">Summarize meeting</span>
      <span class="chat-chip" onclick="window.location='chat.php?q=Draft+a+monthly+investor+update+email'">Draft investor update</span>
      <span class="chat-chip" onclick="window.location='chat.php?q=Analyze+our+burn+rate+and+give+recommendations'">Analyze burn rate</span>
      <span class="chat-chip" onclick="window.location='chat.php?q=Review+this+contract+for+red+flags'">Review contract</span>
    </div>
  </div>

  <!-- 4 FEATURE CARDS -->
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;">
    <a href="gmail.php" class="action-card">
      <div class="ac-icon indigo"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
      <div class="ac-title">Gmail Analysis</div>
      <div class="ac-desc">Read inbox. AI drafts replies. Auto follow-up engine detects missed leads.</div>
      <div class="ac-meta"><?= $gok ? '● Follow-up Engine active' : '○ Connect Google' ?></div>
    </a>
    <a href="sheets.php" class="action-card">
      <div class="ac-icon green"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg></div>
      <div class="ac-title">Sheets Intel</div>
      <div class="ac-desc">Browse Drive. Load live data. AI detects anomalies and forecasts trends.</div>
      <div class="ac-meta"><?= $gok ? '● Drive connected' : '○ Connect Google' ?></div>
    </a>
    <a href="tasks.php" class="action-card">
      <div class="ac-icon amber"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
      <div class="ac-title">Open Loops</div>
      <div class="ac-desc">Chaos Capture: brain dump everything. AI extracts prioritized actions.</div>
      <div class="ac-meta"><?= $tasks ?> active loop<?= $tasks != 1 ? 's' : '' ?></div>
    </a>
    <a href="pitch.php" class="action-card">
      <div class="ac-icon red"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
      <div class="ac-title">Pitch Intelligence</div>
      <div class="ac-desc">YC prep. Tough investor questions. Deck gap analysis. Live coaching.</div>
      <div class="ac-meta">● Ready</div>
    </a>
  </div>

  <!-- COMMAND BAR -->
  <div style="background:var(--white);border:1.5px solid var(--border);border-radius:var(--radius-lg);padding:4px 16px;display:flex;align-items:center;gap:10px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--ink4);flex-shrink:0;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input style="flex:1;border:none;outline:none;font-family:Inter,sans-serif;font-size:14px;color:var(--ink);padding:13px 0;background:transparent;" placeholder="Instruct your Chief of Staff..." id="ci" onkeydown="if(event.key==='Enter'&&this.value.trim())window.location='chat.php?q='+encodeURIComponent(this.value)"/>
    <button onclick="if(document.getElementById('ci').value.trim())window.location='chat.php?q='+encodeURIComponent(document.getElementById('ci').value)" style="background:var(--indigo);color:white;border:none;border-radius:7px;width:30px;height:30px;display:flex;align-items:center;justify-content:center;cursor:pointer;flex-shrink:0;">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
    </button>
  </div>
  <div style="text-align:center;font-size:11px;color:var(--ink4);margin-top:6px;">PRESS <kbd style="background:var(--bg);border:1px solid var(--border);border-radius:3px;padding:1px 5px;font-family:inherit;">ENTER</kbd> FOR SHORTCUTS</div>
</div>
<style>@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}</style>
<?php include __DIR__ . '/includes/footer.php'; ?>
