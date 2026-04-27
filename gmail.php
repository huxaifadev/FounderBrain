<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Gmail Analysis — FounderBrain';
$gok = hasGoogleAuth();
include __DIR__ . '/includes/header.php';
?>

<div class="topbar">
  <div class="topbar-breadcrumb">
    <span class="crumb-main">Gmail Analysis</span>
  </div>
  <div class="topbar-actions">
    <?php if($gok): ?>
    <button class="btn btn-white btn-sm" onclick="loadEmails()">
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-10.36"/></svg>
      Refresh
    </button>
    <button class="btn btn-indigo btn-sm" onclick="scanFollowups()">
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      Scan Follow-ups
    </button>
    <?php endif; ?>
  </div>
</div>

<?php if(!$gok): ?>
<!-- Not connected -->
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;flex:1;padding:60px 20px;text-align:center;">
  <div style="width:60px;height:60px;background:var(--indigobg);border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
    <svg width="26" height="26" fill="none" stroke="var(--indigo)" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
  </div>
  <div style="font-size:22px;font-weight:800;color:var(--ink);letter-spacing:-.4px;margin-bottom:8px;">Connect your Gmail</div>
  <div style="font-size:14px;color:var(--ink3);line-height:1.7;margin-bottom:24px;max-width:380px;">Read your inbox, get AI analysis on every email, and use the Auto Follow-up Engine to never miss a lead again.</div>
  <a href="<?=BASE_URL?>/auth/google.php" class="btn btn-indigo btn-lg">Connect Google Account</a>
</div>

<?php else: ?>

<!-- Follow-up banner -->
<div id="followupBanner" style="display:none;background:linear-gradient(135deg,var(--indigo),#7c3aed);color:white;padding:12px 24px;display:none;align-items:center;gap:12px;flex-shrink:0;">
  <svg width="16" height="16" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
  <span id="bannerText" style="font-size:13.5px;font-weight:500;flex:1;"></span>
  <button onclick="this.parentElement.style.display='none'" style="background:rgba(255,255,255,.2);border:none;color:white;border-radius:6px;padding:4px 10px;font-size:12px;cursor:pointer;">Dismiss</button>
</div>

<div class="two-col">
  <!-- LEFT: Email list -->
  <div class="col-l">
    <div class="inbox-head">
      <span class="inbox-title">Priority Inbox</span>
      <div style="display:flex;gap:6px;align-items:center;">
        <span id="unreadBadge" style="font-size:11px;color:var(--ink3);"></span>
        <select id="filterSel" onchange="filterEmails()" style="border:1px solid var(--border);border-radius:6px;padding:4px 8px;font-size:12px;color:var(--ink2);background:var(--white);outline:none;cursor:pointer;">
          <option value="all">All</option>
          <option value="unread">Unread</option>
          <option value="followup">Follow-up needed</option>
        </select>
      </div>
    </div>
    <div id="emailList">
      <div class="loading"><span></span><span></span><span></span></div>
    </div>
  </div>

  <!-- RIGHT: Email detail -->
  <div class="col-r">
    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;padding:40px 20px;text-align:center;color:var(--ink3);" id="emailEmpty">
      <div style="font-size:32px;margin-bottom:12px;">📬</div>
      <div style="font-size:14px;line-height:1.7;">Select an email to read it.<br>AI will instantly analyze it<br>and draft a reply.</div>
    </div>
    <div id="emailDetail" style="display:none;"></div>
  </div>
</div>

<script>
let allEmails = [];
let activeIdx  = -1;

async function loadEmails(){
  document.getElementById('emailList').innerHTML = '<div class="loading"><span></span><span></span><span></span></div>';
  document.getElementById('emailEmpty').style.display = 'flex';
  document.getElementById('emailDetail').style.display = 'none';

  const res  = await fetch('api/gmail.php?action=list');
  const data = await res.json();
  if (data.error) {
    document.getElementById('emailList').innerHTML = `<div style="padding:16px"><div class="alert alert-error">${esc(data.error)}</div></div>`;
    return;
  }
  allEmails = data.emails || [];
  document.getElementById('unreadBadge').textContent = allEmails.filter(e=>e.unread).length + ' unread';
  renderList(allEmails);
}

function renderList(emails){
  if (!emails.length) {
    document.getElementById('emailList').innerHTML = '<div style="padding:40px 20px;text-align:center;color:var(--ink3);font-size:13.5px;">📭 No emails found.</div>';
    return;
  }
  document.getElementById('emailList').innerHTML = emails.map((e,i) => `
    <div class="email-item ${e.unread?'unread':''} ${e.needsFollowup?'followup-needed':''}" id="ei-${i}" onclick="openEmail(${i})">
      <div class="e-top">
        <span class="e-from">${esc(e.from_name||e.from)}</span>
        <span class="e-time">${esc(e.date)}</span>
      </div>
      <div class="e-subject">${esc(e.subject)}</div>
      <div class="e-preview">${esc(e.snippet)}</div>
      ${e.needsFollowup ? `
        <div style="margin-top:6px;display:flex;align-items:center;gap:5px;">
          <span style="width:6px;height:6px;border-radius:50%;background:var(--red);flex-shrink:0;"></span>
          <span class="e-ai">${esc(e.followupReason||'Follow-up needed')}</span>
        </div>` : ''}
      ${e.urgency ? `<div style="margin-top:5px;"><span class="urgency-pill up-${e.urgency}">${e.urgency} urgency</span></div>` : ''}
    </div>`).join('');
}

function filterEmails(){
  const f = document.getElementById('filterSel').value;
  if (f==='all')      renderList(allEmails);
  else if (f==='unread')   renderList(allEmails.filter(e=>e.unread));
  else              renderList(allEmails.filter(e=>e.needsFollowup));
}

async function openEmail(i){
  activeIdx = i;
  document.querySelectorAll('.email-item').forEach(el=>el.classList.remove('active'));
  document.getElementById('ei-'+i)?.classList.add('active');

  const e = allEmails[i];
  document.getElementById('emailEmpty').style.display = 'none';
  const detail = document.getElementById('emailDetail');
  detail.style.display = 'block';

  const initials = (e.from_name||e.from||'?').split(' ').map(w=>w[0]||'').join('').toUpperCase().slice(0,2);
  detail.innerHTML = `
    <div class="e-detail">
      <div class="e-detail-top">
        <div class="sender-av">${esc(initials)}</div>
        <div style="flex:1">
          <div class="sender-name">${esc(e.from_name||e.from)}</div>
          <div class="sender-to">To: Founder (Chief of Staff)</div>
        </div>
        <button class="btn btn-white btn-sm" onclick="document.getElementById('replyTextarea').focus()">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 17 4 12 9 7"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/></svg>
          Reply
        </button>
      </div>

      <div class="e-subject-big">${esc(e.subject)}</div>
      <div class="e-body">${esc(e.body||e.snippet)}</div>

      <!-- AI panel loading -->
      <div id="aiPanelWrap">
        <div class="ai-panel">
          <div class="ai-panel-label">
            <svg width="12" height="12" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            AI Analysis &amp; Intelligence
          </div>
          <div style="text-align:center;padding:10px 0;opacity:.7;font-size:13px;">
            <div style="display:flex;gap:5px;justify-content:center;margin-bottom:6px;">
              <span style="width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.6);animation:bounce 1.2s infinite;display:inline-block;"></span>
              <span style="width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.6);animation:bounce 1.2s .2s infinite;display:inline-block;"></span>
              <span style="width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.6);animation:bounce 1.2s .4s infinite;display:inline-block;"></span>
            </div>
            Analyzing email...
          </div>
        </div>
      </div>

      <!-- Reply -->
      <div class="reply-wrap" style="margin-top:16px;">
        <div class="reply-label">Draft Reply</div>
        <textarea id="replyTextarea" class="reply-ta" rows="4" placeholder="AI is drafting your reply..."></textarea>
        <div style="display:flex;gap:8px;margin-top:10px;">
          <button class="btn btn-indigo" onclick="sendReply('${esc(e.id)}','${esc(e.from)}','${esc(e.subject)}')">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            Send Reply
          </button>
          <button class="btn btn-white" onclick="analyzeEmail(${i})">Re-analyze</button>
        </div>
      </div>
    </div>`;

  analyzeEmail(i);
}

async function analyzeEmail(i){
  const e = allEmails[i];
  const res  = await fetch('api/chat.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({mode:'gmail', messages:[{role:'user', content:`From: ${e.from}\nSubject: ${e.subject}\nDate: ${e.date}\n\n${e.body||e.snippet}`}]})
  });
  const data = await res.json();
  const text = data.reply || '';

  const sentiment = extractSection(text, 'contextual sentiment') || 'Analyzing tone...';
  const action    = extractSection(text, 'action recommended') || 'Review and reply.';
  const draft     = extractDraft(text);

  const wrap = document.getElementById('aiPanelWrap');
  if (wrap) wrap.innerHTML = `
    <div class="ai-panel">
      <div class="ai-panel-label">
        <svg width="12" height="12" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        AI Analysis &amp; Intelligence
      </div>
      <div class="ai-grid">
        <div class="ai-card-inner">
          <div class="ai-card-lbl">Contextual Sentiment</div>
          <div class="ai-card-val">${esc(sentiment)}</div>
        </div>
        <div class="ai-card-inner">
          <div class="ai-card-lbl">Action Recommended</div>
          <div class="ai-card-val">${esc(action)}</div>
        </div>
      </div>
      ${e.needsFollowup ? `
      <div class="followup-engine">
        <div class="fu-label">⚠️ Auto Follow-up Engine</div>
        <div class="fu-text">${esc(e.followupReason||'You have not replied to this email.')}</div>
        <button class="fu-btn" onclick="sendAutoFollowup(${i})" id="fuBtn-${i}">
          <svg width="13" height="13" fill="none" stroke="var(--indigo)" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
          Send Smart Follow-up
        </button>
      </div>` : ''}
    </div>`;

  const ta = document.getElementById('replyTextarea');
  if (ta && draft) ta.value = draft;
}

async function sendAutoFollowup(i){
  const e   = allEmails[i];
  const btn = document.getElementById('fuBtn-'+i);
  if (btn) { btn.textContent='Generating...'; btn.disabled=true; }

  const res  = await fetch('api/chat.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({mode:'followup', messages:[{role:'user', content:`Original email:\nFrom: ${e.from}\nSubject: ${e.subject}\n\n${e.body||e.snippet}\n\nI never replied. Write a brief, warm, professional follow-up.`}]})
  });
  const data = await res.json();
  const followupText = data.reply || "Following up on my previous message — wanted to make sure this didn't slip through. Happy to connect this week!";

  const sendRes  = await fetch('api/gmail.php', {
    method:'POST', headers:{'Content-Type':'application/json'},
    body: JSON.stringify({action:'send', to:e.from, subject:'Re: '+e.subject, body:followupText, threadId:e.id})
  });
  const sendData = await sendRes.json();

  if (sendData.success) {
    allEmails[i].needsFollowup = false;
    if (btn) { btn.innerHTML='✓ Follow-up Sent!'; btn.style.background='#d1fae5'; btn.style.color='#065f46'; }
    showToast('✅ Smart follow-up sent to '+esc(e.from_name||e.from)+'!', 'success');
  } else {
    if (btn) { btn.textContent='Send Smart Follow-up'; btn.disabled=false; }
    showToast('Failed to send. Check Google connection.', 'error');
  }
}

async function scanFollowups(){
  showToast('🔍 Scanning for missed follow-ups...', 'info');
  const res  = await fetch('api/gmail.php?action=scan_followups');
  const data = await res.json();
  const count = data.count || 0;
  if (count > 0) {
    const banner = document.getElementById('followupBanner');
    document.getElementById('bannerText').textContent = `⚠️ You forgot to reply to ${count} email${count>1?'s':''} — ${(data.emails||[]).map(e=>e.from_name||e.from).join(', ')}`;
    banner.style.display = 'flex';
    data.emails?.forEach(fe => {
      const idx = allEmails.findIndex(e=>e.id===fe.id);
      if (idx>=0) { allEmails[idx].needsFollowup=true; allEmails[idx].followupReason=fe.followupReason; }
      else allEmails.unshift({...fe, needsFollowup:true});
    });
    renderList(allEmails);
    showToast(`Found ${count} email${count>1?'s':''} needing follow-up!`, 'warn');
  } else {
    showToast('✅ All caught up! No missed follow-ups.', 'success');
  }
}

async function sendReply(emailId, to, subject){
  const body = document.getElementById('replyTextarea')?.value?.trim();
  if (!body) { alert('Reply is empty.'); return; }
  const res  = await fetch('api/gmail.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'send', to, subject:'Re: '+subject, body, threadId:emailId})});
  const data = await res.json();
  if (data.success) showToast('✅ Reply sent!', 'success');
  else showToast('Error: '+(data.error||'Failed to send.'), 'error');
}

// ── Helpers ──
function extractSection(text, key){
  const r = new RegExp(key+'[:\\s]*([^\\n*#]{10,150})','i');
  const m = text.match(r); return m ? m[1].trim() : '';
}
function extractDraft(text){
  const patterns = [
    /(?:draft reply|reply draft|suggested reply)[:\s]*\n+([\s\S]{20,400}?)(?:\n\n|$)/i,
    /(?:here(?:'s| is) (?:a |the )?(?:draft|reply))[:\s]*\n+([\s\S]{20,400}?)(?:\n\n|$)/i,
  ];
  for (const p of patterns) { const m=text.match(p); if(m) return m[1].trim(); }
  const lines = text.split('\n');
  const start = lines.findIndex(l=>/^(hi|hello|dear|hey|thanks|thank you|following)/i.test(l.trim()));
  if (start>=0) return lines.slice(start, start+8).join('\n').trim();
  return '';
}
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function showToast(msg, type='info'){
  const colors={info:'var(--indigo)',success:'var(--green)',warn:'var(--amber)',error:'var(--red)'};
  const el=document.createElement('div');
  el.style.cssText=`position:fixed;bottom:24px;right:24px;z-index:9999;background:var(--white);border:1px solid var(--border);border-left:3px solid ${colors[type]};border-radius:var(--radius);padding:12px 16px;font-size:13px;color:var(--ink);box-shadow:var(--shadow-md);max-width:320px;animation:fadeUp .2s ease;`;
  el.textContent=msg; document.body.appendChild(el);
  setTimeout(()=>el.remove(), 4000);
}

loadEmails();
</script>

<style>
@keyframes bounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1);opacity:1}}
@keyframes fadeUp{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:none}}
.followup-needed{background:#fff8f8!important;}
</style>

<?php endif; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
