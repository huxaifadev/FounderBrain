<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Command Center — FounderBrain';
$preQ = htmlspecialchars($_GET['q'] ?? '');

$demos = [
  ['label'=>'Summarize investor call','prompt'=>'I just had a 45-min investor call with a Series A fund. Key topics: product-market fit, team, burn rate $18k/month, 8 months runway, 28 customers, $7.5k MRR growing 20% MoM. They asked tough questions about churn (4%) and competition. Summarize this call and give me the 3 most important follow-ups to send today.'],
  ['label'=>'Draft investor update','prompt'=>'Draft a monthly investor update email. B2B SaaS startup. MRR: $7,500. Customers: 33. MoM Growth: 21%. Burn: $18k/month. Runway: 6 months. This month we launched our AI feature and onboarded Acme Corp. Key challenge: hiring a senior engineer.'],
  ['label'=>'Analyze burn rate','prompt'=>'Our monthly burn is $18,000. Revenue is $7,500 MRR. Runway: 6 months. 3 team members. Costs: salaries $12k, infrastructure $3k, SaaS tools $1.2k, marketing $1.8k. Should we be worried? What should we cut first?'],
  ['label'=>'YC application help','prompt'=>'Help me answer the YC application question: "Describe what your company does in 50 characters or less." We build AI tools that help startup founders reduce operational overhead by connecting their tools and automating their busywork.'],
  ['label'=>'Review contract','prompt'=>'I received a contract from a new API vendor. Key terms: $500/month, auto-renews annually, 30 days cancellation notice, data processed on EU servers, liability capped at 3 months fees, they can change pricing with 30 days notice. What are the red flags?'],
  ['label'=>'Cold email to VC','prompt'=>'Draft a cold email to a seed-stage VC partner focused on B2B SaaS. Our startup: AI Chief of Staff for founders. $7.5k MRR, 33 customers, 21% MoM growth. Raising $500k pre-seed. Keep it under 150 words.'],
];

include __DIR__ . '/includes/header.php';
?>

<div class="topbar">
  <div class="topbar-breadcrumb">
    <span class="crumb-main">Chief of Staff</span>
  </div>
  <div class="topbar-actions">
    <button class="btn btn-white btn-sm" onclick="clearChat()">Clear</button>
    <button class="btn btn-white btn-sm" onclick="document.getElementById('demoModal').classList.remove('hidden')">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      Demo Scenarios
    </button>
  </div>
</div>

<div class="chat-wrap">
  <!-- Messages area -->
  <div class="chat-msgs" id="chatMessages">
    <div class="cmsg">
      <div class="cmsg-av ai">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
      </div>
      <div class="cmsg-bubble ai">
        <strong>Good <?= (int)date('H') < 12 ? 'morning' : ((int)date('H') < 17 ? 'afternoon' : 'evening') ?>.</strong> I'm your AI Chief of Staff.<br><br>
        Paste anything — an investor email, a Slack thread, a half-written YC application, a customer complaint. I'll tell you exactly what to do, draft the reply, and flag what you're missing.<br><br>
        What's the most urgent thing right now?
      </div>
    </div>
  </div>

  <!-- Input area pinned at bottom -->
  <div class="chat-input-area">
    <div class="chat-chips">
      <span class="chat-chip" onclick="setQ('Draft a follow-up email to an investor who went quiet after our last meeting.')">📧 Investor follow-up</span>
      <span class="chat-chip" onclick="setQ('Help me answer YC application: What does your company do in 50 characters?')">📝 YC app</span>
      <span class="chat-chip" onclick="setQ('Review my week and tell me what I might be forgetting or should prioritize today.')">🔍 Weekly review</span>
      <span class="chat-chip" onclick="setQ('Draft a customer onboarding welcome email for a new SaaS signup.')">👋 Onboard customer</span>
    </div>
    <div class="input-box">
      <textarea id="chatInput" rows="1" placeholder="Instruct your Chief of Staff..." onkeydown="handleKey(event)" oninput="autoResize(this)"><?= $preQ ?></textarea>
      <button class="send-btn" onclick="sendMsg()" id="sendBtn">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </div>
  </div>
</div>

<!-- Demo Modal -->
<div style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:500;align-items:center;justify-content:center;padding:20px;" id="demoModal" onclick="if(event.target===this)closeDemoModal()">
  <div style="background:var(--white);border-radius:var(--radius-xl);border:1px solid var(--border);box-shadow:var(--shadow-lg);max-width:520px;width:100%;max-height:80vh;overflow-y:auto;">
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
      <span style="font-size:15px;font-weight:700;color:var(--ink);">Demo Scenarios</span>
      <button onclick="closeDemoModal()" style="background:none;border:none;cursor:pointer;color:var(--ink3);font-size:18px;padding:2px 6px;">✕</button>
    </div>
    <div style="padding:12px;">
      <?php foreach($demos as $d): ?>
      <div onclick="useDemo(<?= htmlspecialchars(json_encode($d['prompt'])) ?>)"
        style="padding:11px 14px;border-radius:var(--radius);cursor:pointer;border:1px solid var(--border);margin-bottom:7px;transition:all .12s;"
        onmouseover="this.style.background='var(--indigobg)';this.style.borderColor='var(--indigo)'"
        onmouseout="this.style.background='';this.style.borderColor='var(--border)'">
        <div style="font-size:13px;font-weight:600;color:var(--ink);margin-bottom:3px;"><?= htmlspecialchars($d['label']) ?></div>
        <div style="font-size:12px;color:var(--ink3);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars(substr($d['prompt'],0,90)) ?>...</div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
let history = [];

function setQ(t){ document.getElementById('chatInput').value=t; document.getElementById('chatInput').focus(); }
function handleKey(e){ if(e.key==='Enter'&&!e.shiftKey){e.preventDefault();sendMsg();} }
function autoResize(el){ el.style.height='auto'; el.style.height=Math.min(el.scrollHeight,140)+'px'; }

function openDemoModal(){ const m=document.getElementById('demoModal'); m.style.display='flex'; }
function closeDemoModal(){ const m=document.getElementById('demoModal'); m.style.display='none'; }

function useDemo(prompt){
  closeDemoModal();
  document.getElementById('chatInput').value = prompt;
  sendMsg();
}

function clearChat(){
  history = [];
  document.getElementById('chatMessages').innerHTML = `
    <div class="cmsg">
      <div class="cmsg-av ai"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div>
      <div class="cmsg-bubble ai">Chat cleared. What's on your plate?</div>
    </div>`;
}

async function sendMsg(){
  const input = document.getElementById('chatInput');
  const text  = input.value.trim();
  if (!text) return;
  addMsg('user', text);
  history.push({role:'user', content:text});
  input.value = ''; input.style.height = 'auto';
  const btn = document.getElementById('sendBtn');
  btn.disabled = true;
  const tid = addThinking();
  try {
    const res  = await fetch('api/chat.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({messages:history, mode:'chat'})});
    const data = await res.json();
    removeEl(tid);
    const reply = data.reply || 'Something went wrong.';
    addMsg('assistant', reply);
    history.push({role:'assistant', content:reply});
  } catch(e) {
    removeEl(tid);
    addMsg('assistant', '⚠️ Connection error. Check config.php.');
  }
  btn.disabled = false;
}

function addMsg(role, text){
  const area = document.getElementById('chatMessages');
  const div  = document.createElement('div');
  div.className = 'cmsg' + (role==='user' ? ' user-m' : '');
  const fmt = text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/\*(.*?)\*/g, '<em>$1</em>')
    .replace(/^### (.*$)/gm, '<h4>$1</h4>')
    .replace(/^## (.*$)/gm,  '<h4>$1</h4>')
    .replace(/^- (.*$)/gm,   '<li>$1</li>')
    .replace(/\n/g, '<br>');
  div.innerHTML = role === 'assistant'
    ? `<div class="cmsg-av ai"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div><div class="cmsg-bubble ai">${fmt}</div>`
    : `<div class="cmsg-bubble user-b">${text}</div><div class="cmsg-av user">👤</div>`;
  area.appendChild(div);
  area.scrollTop = area.scrollHeight;
}

function addThinking(){
  const area = document.getElementById('chatMessages');
  const div  = document.createElement('div');
  const id   = 't' + Date.now();
  div.id = id; div.className = 'cmsg';
  div.innerHTML = `<div class="cmsg-av ai"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></div><div class="cmsg-bubble ai"><div class="think"><span></span><span></span><span></span></div></div>`;
  area.appendChild(div);
  area.scrollTop = area.scrollHeight;
  return id;
}

function removeEl(id){ const el=document.getElementById(id); if(el)el.remove(); }

<?php if($preQ): ?>
window.addEventListener('load', ()=>setTimeout(sendMsg, 400));
<?php endif; ?>
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
