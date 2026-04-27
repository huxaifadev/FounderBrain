<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Pitch Intelligence — FounderBrain';
include __DIR__ . '/includes/header.php';

$toughQs = [
  ['text'=>'"How will you defend against Amazon entering this space next year?"','cls'=>'tq-red'],
  ['text'=>'"What is the specific bottleneck preventing 10x scale today?"','cls'=>'tq-indigo'],
  ['text'=>'"Why hasn\'t a bigger player solved this already?"','cls'=>'tq-indigo'],
  ['text'=>'"What happens to your unit economics at 10k customers?"','cls'=>'tq-red'],
];
?>

<div class="topbar">
  <div class="topbar-breadcrumb">
    <span class="crumb-main">Pitch Intelligence</span>
    <span class="crumb-sep">|</span>
    <span class="crumb-sub">Series B Prep Session</span>
  </div>
  <div class="topbar-actions">
    <div class="tb-icon"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg></div>
  </div>
</div>

<div class="pitch-grid">
  <!-- MAIN -->
  <div class="pitch-main">
    <div id="setupSection">
      <div style="margin-bottom:20px;">
        <div style="font-size:14px;font-weight:700;color:var(--ink);margin-bottom:3px;">Startup DNA</div>
        <div style="font-size:13px;color:var(--ink3);">Configure the intelligence engine for your next high-stakes meeting.</div>
      </div>

      <div style="display:flex;justify-content:flex-end;margin-bottom:12px;">
        <button class="btn btn-white btn-sm">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.46"/></svg>
          Load Previous
        </button>
      </div>

      <div style="background:var(--white);border:1.5px solid var(--border);border-radius:var(--radius-xl);padding:22px;">
        <div style="font-size:10.5px;font-weight:700;color:var(--indigo);letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
          <svg width="13" height="13" fill="none" stroke="var(--indigo)" stroke-width="2.5" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Describe Your Startup
        </div>
        <textarea class="dna-ta" id="desc" placeholder="Describe your product, vision, and the core problem you're solving..."></textarea>
        <div style="font-size:11.5px;color:var(--ink3);font-style:italic;margin-top:6px;margin-bottom:16px;">AI Tip: Focus on the 'Unfair Advantage' and 'GTM Strategy' for better results.</div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px;">
          <div>
            <div style="font-size:10.5px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Key Metrics</div>
            <input class="mp-input" id="metrics" placeholder="e.g., $2.4M ARR, 15% MoM Growth"/>
          </div>
          <div>
            <div style="font-size:10.5px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Prep Type</div>
            <select class="mp-select" id="prepType">
              <option value="yc">YC Interview Prep</option>
              <option value="investor">Investor Meeting</option>
              <option value="accelerator">Accelerator Application</option>
              <option value="oneliner">Refine One-Liner</option>
            </select>
          </div>
        </div>

        <button onclick="startPitch()" id="startBtn" style="width:100%;background:var(--indigo);color:white;border:none;border-radius:var(--radius-lg);padding:14px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:9px;transition:all .15s;font-family:Inter,sans-serif;margin-bottom:18px;" onmouseover="this.style.background='var(--indigo2)'" onmouseout="this.style.background='var(--indigo)'">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
          Start Pitch Session
        </button>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
          <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:12px 14px;">
            <div style="font-size:10px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">Current Topic</div>
            <div style="font-size:14px;font-weight:700;color:var(--ink);">Unit Economics</div>
          </div>
          <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:12px 14px;">
            <div style="font-size:10px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">Stress Level</div>
            <div style="font-size:14px;font-weight:700;color:var(--ink);">High Confidence</div>
          </div>
          <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius);padding:12px 14px;">
            <div style="font-size:10px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:4px;">Time to Event</div>
            <div style="font-size:14px;font-weight:700;color:var(--ink);">4 Days Left</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pitch chat -->
    <div id="pitchChatSection" class="hidden" style="margin-top:16px;">
      <div style="display:flex;flex-direction:column;min-height:300px;">
        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:14px;" id="pitchMsgs"></div>
        <div style="display:flex;gap:8px;margin-top:auto;">
          <input style="flex:1;border:1.5px solid var(--border);border-radius:var(--radius);padding:10px 13px;font-family:Inter,sans-serif;font-size:13px;color:var(--ink);outline:none;transition:border-color .12s;" id="pa" placeholder="Answer the question..." onfocus="this.style.borderColor='var(--indigo)'" onblur="this.style.borderColor='var(--border)'" onkeydown="if(event.key==='Enter')sendPA()"/>
          <button class="btn btn-indigo" onclick="sendPA()">Answer →</button>
          <button class="btn btn-white" onclick="resetPitch()">← Start Over</button>
        </div>
      </div>
    </div>
  </div>

  <!-- SIDE: Intelligence panel -->
  <div class="pitch-side">
    <div class="ps-head">
      <span class="ps-title">Prep Intelligence</span>
      <span class="ps-badge">LIVE ANALYSIS</span>
    </div>

    <div class="ps-section">
      <div class="ps-section-title">Deck Analysis</div>
      <div class="deck-card">
        <div style="display:flex;align-items:flex-start;gap:10px;">
          <div style="width:36px;height:36px;background:var(--indigobg);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="16" height="16" fill="none" stroke="var(--indigo)" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
          <div>
            <div class="deck-title">Deck v2.4 analyzed</div>
            <div class="deck-sub">Found 3 logic gaps in the competitive landscape slides.</div>
          </div>
        </div>
        <div class="deck-progress"><div class="deck-progress-bar"></div></div>
      </div>
    </div>

    <div class="ps-section">
      <div class="ps-section-title">Recent Transcript Gaps</div>
      <div style="background:var(--bg);border-radius:var(--radius);padding:11px 13px;display:flex;align-items:flex-start;gap:9px;">
        <div style="width:26px;height:26px;border-radius:50%;background:var(--indigo);color:white;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:10px;font-weight:700;">AI</div>
        <div style="font-size:12.5px;color:var(--ink2);line-height:1.6;">"Your answer on churn was defensive. Try focusing on the Cohort LTV instead."</div>
      </div>
    </div>

    <div class="ps-section" style="flex:1;">
      <div class="ps-section-title">Tough Questions</div>
      <?php foreach($toughQs as $q): ?>
      <div class="tough-q <?=$q['cls']?>" onclick="useQ(<?=htmlspecialchars(json_encode($q['text']))?>)">
        <span class="tough-q-text"><?=htmlspecialchars($q['text'])?></span>
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="padding:14px 18px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
      <span style="font-size:13px;font-weight:600;color:var(--ink);">View All Tough Questions</span>
      <span style="font-size:13px;font-weight:700;color:var(--indigo);">14 Total</span>
    </div>
  </div>
</div>

<script>
let ph = [];

async function startPitch(){
  const desc = document.getElementById('desc').value.trim();
  if (!desc){ alert('Describe your startup first.'); return; }
  const btn = document.getElementById('startBtn'); btn.disabled=true; btn.textContent='Loading...';
  const labels = {yc:'YC Interview',investor:'Investor Meeting',accelerator:'Accelerator Application',oneliner:'One-Liner Refinement'};
  const t = document.getElementById('prepType').value;
  const msg = `Prep type: ${labels[t]}\nStartup: ${desc}\nMetrics: ${document.getElementById('metrics').value||'not provided'}\n\nBegin. Ask your first tough question.`;
  ph = [{role:'user', content:msg}];
  document.getElementById('pitchChatSection').classList.remove('hidden');
  await callPitch();
  btn.disabled=false; btn.textContent='Start Pitch Session';
  document.getElementById('pitchChatSection').scrollIntoView({behavior:'smooth'});
}

function useQ(q){
  if (!ph.length){ alert('Start a pitch session first.'); return; }
  addPM('user', q); ph.push({role:'user', content:q}); callPitch();
}

async function sendPA(){
  const t=document.getElementById('pa').value.trim(); if(!t) return;
  addPM('user',t); ph.push({role:'user',content:t}); document.getElementById('pa').value='';
  await callPitch();
}

async function callPitch(){
  const tid=addPT();
  const res=await fetch('api/chat.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({mode:'pitch',messages:ph})});
  const data=await res.json(); removeEl(tid);
  const r=data.reply||''; addPM('assistant',r); ph.push({role:'assistant',content:r});
}

function addPM(role, text){
  const area = document.getElementById('pitchMsgs');
  const div  = document.createElement('div');
  div.className = 'pmsg' + (role==='user' ? ' user-p' : '');
  const fmt = text.replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>').replace(/\n/g,'<br>');
  div.innerHTML = role==='assistant'
    ? `<div class="pmsg-av ai">AI</div><div class="pmsg-bubble ai">${fmt}</div>`
    : `<div class="pmsg-bubble user-b">${text}</div><div class="pmsg-av user">👤</div>`;
  area.appendChild(div); area.scrollTop=area.scrollHeight;
}

function addPT(){
  const area=document.getElementById('pitchMsgs'); const div=document.createElement('div');
  const id='pt'+Date.now(); div.id=id; div.className='pmsg';
  div.innerHTML=`<div class="pmsg-av ai">AI</div><div class="pmsg-bubble ai"><div style="display:flex;gap:5px;padding:2px 0;"><span style="width:6px;height:6px;border-radius:50%;background:var(--ink3);animation:bounce 1.2s infinite;display:inline-block;"></span><span style="width:6px;height:6px;border-radius:50%;background:var(--ink3);animation:bounce 1.2s .2s infinite;display:inline-block;"></span><span style="width:6px;height:6px;border-radius:50%;background:var(--ink3);animation:bounce 1.2s .4s infinite;display:inline-block;"></span></div></div>`;
  area.appendChild(div); area.scrollTop=area.scrollHeight; return id;
}
function removeEl(id){const el=document.getElementById(id);if(el)el.remove();}
function resetPitch(){ph=[];document.getElementById('pitchMsgs').innerHTML='';document.getElementById('pitchChatSection').classList.add('hidden');}
</script>
<style>@keyframes bounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1);opacity:1}}</style>
<?php include __DIR__ . '/includes/footer.php'; ?>
