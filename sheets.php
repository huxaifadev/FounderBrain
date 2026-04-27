<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Sheets Intelligence — FounderBrain';
$gok = hasGoogleAuth();
include __DIR__ . '/includes/header.php';

$demos = [
  ['name'=>'Q4 Revenue Analysis',  'icon'=>'📊', 'mod'=>'Updated 2h ago', 'size'=>'1.2 MB'],
  ['name'=>'Marketing Pipeline',   'icon'=>'📋', 'mod'=>'Updated 1d ago', 'size'=>'840 KB'],
  ['name'=>'Employee Directory',   'icon'=>'👥', 'mod'=>'Updated 3d ago', 'size'=>'2.4 MB'],
  ['name'=>'Product Inventory',    'icon'=>'📦', 'mod'=>'Updated Oct 12', 'size'=>'4.1 MB'],
];

$demoData = [
  'Q4 Revenue Analysis' => [
    'sheet'   => 'Sheet1: Financials',
    'headers' => ['Transaction ID','Entity','Amount ($)','Region','Status','Date'],
    'rows'    => [
      ['TX-9082','Astra Corp','48,000','North America','Paid','Oct 1'],
      ['TX-9083','Luminary Global','32,500','EMEA','Pending','Oct 3'],
      ['TX-9084','Beacon Systems','61,200','APAC','Paid','Oct 7'],
      ['TX-9085','Zenith Lab','94,800','North America','Overdue','Oct 9'],
      ['TX-9086','Horizon Ventures','27,300','EMEA','Paid','Oct 12'],
    ],
    'insights' => [
      ['label'=>'GROWTH TREND',    'val'=>'+20.4%', 'desc'=>'MRR trending up vs Q3, driven by North America expansion.','color'=>'var(--green)'],
      ['label'=>'PREDICTED OUTLIER','val'=>'⚠️',    'desc'=>'TX-9085 is 3x higher than average for Zenith Lab historicals.','color'=>'var(--amber)'],
    ],
    'ai_msg' => "I've finished analyzing the revenue sheet. EMEA regions show a slight delay in payment cycles. Would you like a breakdown by account manager?",
  ],
  'Marketing Pipeline' => [
    'sheet'   => 'Sheet1: Pipeline',
    'headers' => ['Lead','Company','Stage','Value ($)','Owner','Last Contact'],
    'rows'    => [
      ['Sarah Chen','TechCorp','Proposal','45,000','Alex','2d ago'],
      ['Marcus W','Finova','Demo','28,000','Jordan','Today'],
      ['Priya P','Stackly','Closed Won','72,000','Alex','1w ago'],
      ['Tom R','Nexusio','Prospecting','15,000','Sam','3d ago'],
    ],
    'insights' => [
      ['label'=>'PIPELINE VALUE','val'=>'$160K', 'desc'=>'Total active pipeline. Proposal stage converts at 68%.','color'=>'var(--indigo)'],
      ['label'=>'FORECAST',      'val'=>'$117K', 'desc'=>'Expected close this quarter based on historical rates.','color'=>'var(--green)'],
    ],
    'ai_msg' => "Pipeline looks healthy. 2 leads haven't been contacted in 3+ days — Tom R at Nexusio and Sarah Chen at TechCorp. Want me to draft follow-up emails?",
  ],
  'Employee Directory' => [
    'sheet'   => 'Sheet1: Team',
    'headers' => ['Name','Role','Department','Start Date','Status','Salary Band'],
    'rows'    => [
      ['Alex Chen','CEO','Executive','Jan 2023','Active','Band 5'],
      ['Jordan Lee','CTO','Engineering','Mar 2023','Active','Band 5'],
      ['Sam Park','Head of Sales','Revenue','Jun 2023','Active','Band 4'],
      ['Priya Kumar','Lead Engineer','Engineering','Sep 2023','Active','Band 3'],
    ],
    'insights' => [
      ['label'=>'TEAM SIZE',   'val'=>'4',       'desc'=>'Engineering is largest dept at 50% of team.','color'=>'var(--indigo)'],
      ['label'=>'HIRING NEED', 'val'=>'2 Roles', 'desc'=>'Recommend 1 senior engineer + 1 sales rep for Q4 targets.','color'=>'var(--amber)'],
    ],
    'ai_msg' => "Team is lean for your growth stage. Engineering capacity may become a bottleneck by Q1 if you hit 40+ customers. Start hiring now.",
  ],
  'Product Inventory' => [
    'sheet'   => 'Sheet1: Stock',
    'headers' => ['SKU','Product','Stock','Reorder Point','Supplier','Lead Time'],
    'rows'    => [
      ['P001','Core License','∞','—','Internal','—'],
      ['P002','Pro Add-on','∞','—','Internal','—'],
      ['P003','Enterprise Plan','∞','—','Internal','—'],
      ['P004','API Credits (1M)','4,200','1,000','AWS','3d'],
    ],
    'insights' => [
      ['label'=>'CRITICAL STOCK','val'=>'⚠️ 1','desc'=>'API Credits approaching reorder threshold. Order within 2 weeks.','color'=>'var(--red)'],
      ['label'=>'MRR IMPACT',    'val'=>'$0',   'desc'=>'No stockout risk. All SaaS plans are unlimited digital goods.','color'=>'var(--green)'],
    ],
    'ai_msg' => "Inventory looks fine. Main risk: API credit depletion if you onboard more than 3 enterprise accounts this month.",
  ],
];
?>

<div class="topbar">
  <div class="topbar-breadcrumb">
    <span class="crumb-main">Sheets Intelligence</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-sub" id="sheetBreadcrumb">Q4 Revenue Analysis</span>
  </div>
  <div class="topbar-search" style="margin-left:auto;">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--ink4)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input placeholder="Search analytics..."/>
  </div>
  <div class="topbar-actions">
    <div class="tb-icon" onclick="refreshData()" title="Refresh">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-10.36"/></svg>
    </div>
    <button class="btn btn-indigo btn-sm" onclick="document.getElementById('shareModal').classList.remove('hidden')">Share Analysis</button>
  </div>
</div>

<div class="three-col">

  <!-- COL 1: File list -->
  <div class="col-files">
    <div class="files-header">
      <div class="files-title">Connected Sheets</div>
      <div class="connect-new-label">Connect New</div>
      <div class="url-input-wrap">
        <input class="url-input" id="sheetUrl" placeholder="Paste Google Sheet URL..." onkeydown="if(event.key==='Enter')loadFromUrl()"/>
        <button class="btn btn-indigo btn-sm" onclick="loadFromUrl()">
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
        </button>
      </div>
    </div>

    <!-- Demo sheets — always visible -->
    <div style="padding:8px 16px 6px;font-size:10px;font-weight:700;color:var(--ink4);letter-spacing:.08em;text-transform:uppercase;">Demo Sheets</div>
    <?php foreach($demos as $i => $d): ?>
    <div class="file-item <?= $i===0?'active':'' ?>" id="fi-<?= $i ?>" onclick="openDemo('<?= htmlspecialchars($d['name']) ?>', <?= $i ?>)">
      <div class="file-icon" style="font-size:16px;background:var(--indigobg);"><?= $d['icon'] ?></div>
      <div style="flex:1;min-width:0;">
        <div class="file-name"><?= htmlspecialchars($d['name']) ?></div>
        <div class="file-meta"><span><?= $d['mod'] ?></span><span><?= $d['size'] ?></span></div>
      </div>
    </div>
    <?php endforeach; ?>

    <!-- Google Drive sheets -->
    <?php if($gok): ?>
    <div style="padding:10px 16px 6px;border-top:1px solid var(--border);margin-top:6px;">
      <div style="font-size:10px;font-weight:700;color:var(--ink4);letter-spacing:.08em;text-transform:uppercase;margin-bottom:8px;">From Google Drive</div>
      <div id="driveList">
        <div style="display:flex;gap:5px;align-items:center;padding:8px 0;">
          <span style="width:6px;height:6px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s infinite;display:inline-block;"></span>
          <span style="width:6px;height:6px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s .2s infinite;display:inline-block;"></span>
          <span style="width:6px;height:6px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s .4s infinite;display:inline-block;"></span>
          <span style="font-size:12px;color:var(--ink3);margin-left:4px;">Loading Drive...</span>
        </div>
      </div>
    </div>
    <?php else: ?>
    <div style="padding:12px 16px;border-top:1px solid var(--border);margin-top:6px;">
      <div style="font-size:11.5px;color:var(--ink3);margin-bottom:8px;line-height:1.5;">Connect Google to browse your real Drive spreadsheets.</div>
      <a href="<?= BASE_URL ?>/auth/google.php" class="btn btn-indigo btn-sm" style="width:100%;justify-content:center;">Connect Google</a>
    </div>
    <?php endif; ?>
  </div>

  <!-- COL 2: Data table -->
  <div class="col-data">
    <div class="data-panel-header" id="dataHeader">
      <div class="data-title" id="dataTitle">Q4 Revenue Analysis</div>
      <div class="data-sub">Viewing raw data from <a href="#" id="dataSheetRef">Sheet1: Financials</a></div>
    </div>
    <div style="display:flex;justify-content:flex-end;padding:10px 22px;border-bottom:1px solid var(--border);background:var(--white);">
      <button class="btn btn-white btn-sm" onclick="refreshData()">
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-10.36"/></svg>
        Refresh
      </button>
    </div>
    <div class="data-table-wrap" id="dataTableWrap">
      <!-- Pre-loaded first demo -->
      <table class="dtable">
        <thead><tr>
          <?php foreach($demoData['Q4 Revenue Analysis']['headers'] as $h): ?>
          <th><?= htmlspecialchars($h) ?></th>
          <?php endforeach; ?>
        </tr></thead>
        <tbody>
          <?php foreach($demoData['Q4 Revenue Analysis']['rows'] as $row): ?>
          <tr><?php foreach($row as $cell): ?><td><?= htmlspecialchars($cell) ?></td><?php endforeach; ?></tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- COL 3: AI Intelligence -->
  <div class="col-ai">
    <div class="ai-sidebar">
      <div class="ai-sidebar-head">
        <svg width="14" height="14" fill="none" stroke="var(--indigo)" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        AI Intelligence
      </div>

      <!-- Insight cards -->
      <div id="insightCards">
        <?php foreach($demoData['Q4 Revenue Analysis']['insights'] as $ins): ?>
        <div class="ai-insight-card">
          <div class="ai-insight-label">
            <span><?= htmlspecialchars($ins['label']) ?></span>
            <span style="color:<?= $ins['color'] ?>;font-weight:700;"><?= htmlspecialchars($ins['val']) ?></span>
          </div>
          <div class="ai-insight-val"><?= htmlspecialchars($ins['desc']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- AI chat -->
      <div class="ai-chat-messages" id="aiChatMessages">
        <div class="ai-msg ai-side">
          <div style="display:flex;align-items:flex-start;gap:8px;">
            <div style="width:26px;height:26px;border-radius:50%;background:var(--indigobg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg width="11" height="11" fill="none" stroke="var(--indigo)" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            </div>
            <div id="aiInitMsg" style="font-size:12.5px;line-height:1.6;"><?= htmlspecialchars($demoData['Q4 Revenue Analysis']['ai_msg']) ?></div>
          </div>
        </div>
      </div>

      <!-- Quick chips -->
      <div class="ai-chips">
        <button class="ai-chip-btn" onclick="askAI('Give me a summary of this sheet data.')">Summary</button>
        <button class="ai-chip-btn" onclick="askAI('Find any anomalies or outliers in this data.')">Anomalies</button>
        <button class="ai-chip-btn" onclick="askAI('Forecast the next quarter based on this data.')">Forecast</button>
      </div>

      <!-- AI input -->
      <div class="ai-input-row">
        <input class="ai-input-field" id="aiQ" placeholder="Ask anything about this sheet..." onkeydown="if(event.key==='Enter'&&this.value.trim()){askAI(this.value);}"/>
        <button class="ai-send" onclick="askAI(document.getElementById('aiQ').value)">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Share modal -->
<div id="shareModal" class="hidden" style="position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:500;display:flex;align-items:center;justify-content:center;padding:20px;" onclick="if(event.target===this)this.classList.add('hidden')">
  <div style="background:var(--white);border-radius:var(--radius-xl);padding:24px;max-width:400px;width:100%;box-shadow:var(--shadow-lg);">
    <div style="font-size:16px;font-weight:700;color:var(--ink);margin-bottom:6px;">Share Analysis</div>
    <div style="font-size:13px;color:var(--ink3);margin-bottom:16px;">Export or share this sheet analysis</div>
    <div style="display:flex;flex-direction:column;gap:8px;">
      <button class="btn btn-indigo" style="width:100%;justify-content:center;" onclick="alert('Analysis link copied!');document.getElementById('shareModal').classList.add('hidden')">Copy Analysis Link</button>
      <button class="btn btn-white" style="width:100%;justify-content:center;" onclick="document.getElementById('shareModal').classList.add('hidden')">Cancel</button>
    </div>
  </div>
</div>

<script>
const demoData    = <?= json_encode($demoData) ?>;
let currentSheet  = 'Q4 Revenue Analysis';
let sheetText     = '';

// ── Demo sheet click ──────────────────────────────────────
function openDemo(name, idx) {
  currentSheet = name;
  const d = demoData[name];
  if (!d) return;

  // Highlight active
  document.querySelectorAll('.file-item').forEach(el => el.classList.remove('active'));
  const fi = document.getElementById('fi-' + idx);
  if (fi) fi.classList.add('active');

  // Update breadcrumb + header
  document.getElementById('sheetBreadcrumb').textContent = name;
  document.getElementById('dataTitle').textContent        = name;
  document.getElementById('dataSheetRef').textContent     = d.sheet;

  // Render table & insights
  renderTable(d.headers, d.rows);
  renderInsights(d.insights);

  // Update AI chat
  document.getElementById('aiChatMessages').innerHTML = `
    <div class="ai-msg ai-side">
      <div style="display:flex;align-items:flex-start;gap:8px;">
        <div style="width:26px;height:26px;border-radius:50%;background:var(--indigobg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="11" height="11" fill="none" stroke="var(--indigo)" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        </div>
        <div style="font-size:12.5px;line-height:1.6;">${esc(d.ai_msg)}</div>
      </div>
    </div>`;

  sheetText = [d.headers, ...d.rows].map(r => r.join('\t')).join('\n');
}

// ── Render table ──────────────────────────────────────────
function renderTable(headers, rows) {
  let html = '<table class="dtable"><thead><tr>' + headers.map(h => `<th>${esc(h)}</th>`).join('') + '</tr></thead><tbody>';
  html += rows.map(r => '<tr>' + headers.map((_, i) => `<td>${esc(r[i] || '')}</td>`).join('') + '</tr>').join('');
  html += '</tbody></table>';
  document.getElementById('dataTableWrap').innerHTML = html;
}

// ── Render insight cards ──────────────────────────────────
function renderInsights(insights) {
  document.getElementById('insightCards').innerHTML = insights.map(ins => `
    <div class="ai-insight-card">
      <div class="ai-insight-label">
        <span>${esc(ins.label)}</span>
        <span style="color:${ins.color};font-weight:700;">${esc(ins.val)}</span>
      </div>
      <div class="ai-insight-val">${esc(ins.desc)}</div>
    </div>`).join('');
}

// ── Ask AI about the sheet ────────────────────────────────
async function askAI(q) {
  if (!q || !q.trim()) return;
  document.getElementById('aiQ').value = '';
  const msgs = document.getElementById('aiChatMessages');

  msgs.innerHTML += `<div class="ai-msg user-side">${esc(q)}</div>`;
  const typingId = 'typ' + Date.now();
  msgs.innerHTML += `<div class="ai-msg ai-side" id="${typingId}"><div style="display:flex;gap:5px;align-items:center;padding:3px 0;"><span style="width:6px;height:6px;border-radius:50%;background:var(--ink3);animation:bounce 1.2s infinite;display:inline-block;"></span><span style="width:6px;height:6px;border-radius:50%;background:var(--ink3);animation:bounce 1.2s .2s infinite;display:inline-block;"></span><span style="width:6px;height:6px;border-radius:50%;background:var(--ink3);animation:bounce 1.2s .4s infinite;display:inline-block;"></span></div></div>`;
  msgs.scrollTop = msgs.scrollHeight;

  const ctx = sheetText
    ? `Sheet name: ${currentSheet}\n\nData:\n${sheetText}\n\nQuestion: ${q}`
    : q;

  try {
    const res  = await fetch('api/chat.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({mode:'chat', messages:[{role:'user', content:ctx}]})});
    const data = await res.json();
    document.getElementById(typingId)?.remove();
    const fmt = (data.reply || '').replace(/\*\*(.*?)\*\*/g,'<strong>$1</strong>').replace(/\n/g,'<br>');
    msgs.innerHTML += `
      <div class="ai-msg ai-side">
        <div style="display:flex;align-items:flex-start;gap:8px;">
          <div style="width:26px;height:26px;border-radius:50%;background:var(--indigobg);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="11" height="11" fill="none" stroke="var(--indigo)" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
          </div>
          <div style="font-size:12.5px;line-height:1.6;">${fmt}</div>
        </div>
      </div>`;
  } catch(e) {
    document.getElementById(typingId)?.remove();
    msgs.innerHTML += `<div class="ai-msg ai-side" style="color:var(--red);">⚠️ Error. Check your connection.</div>`;
  }
  msgs.scrollTop = msgs.scrollHeight;
}

// ── Load from pasted URL ──────────────────────────────────
async function loadFromUrl() {
  const url = document.getElementById('sheetUrl').value.trim();
  if (!url) return;
  const m  = url.match(/spreadsheets\/d\/([a-zA-Z0-9-_]+)/);
  const id = m ? m[1] : url;
  loadSheetById(id, url);
}

// ── Load real sheet by Drive ID ───────────────────────────
async function loadSheetById(id, name) {
  currentSheet = name || id;
  document.getElementById('sheetBreadcrumb').textContent = name || 'Loading...';
  document.getElementById('dataTitle').textContent       = name || 'Loading...';
  document.getElementById('dataTableWrap').innerHTML     = '<div style="padding:40px;display:flex;gap:6px;justify-content:center;align-items:center;"><span style="width:8px;height:8px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s infinite;display:inline-block;"></span><span style="width:8px;height:8px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s .2s infinite;display:inline-block;"></span><span style="width:8px;height:8px;border-radius:50%;background:var(--indigo);animation:bounce 1.2s .4s infinite;display:inline-block;"></span></div>';

  try {
    const res  = await fetch(`api/sheets.php?action=data&id=${encodeURIComponent(id)}&range=Sheet1`);
    const data = await res.json();

    if (data.error) {
      document.getElementById('dataTableWrap').innerHTML = `<div style="padding:20px"><div class="alert alert-error">${esc(data.error)}</div></div>`;
      return;
    }

    const rows = data.values || [];
    if (!rows.length) {
      document.getElementById('dataTableWrap').innerHTML = '<div style="padding:20px;color:var(--ink3);font-size:13px;">No data found in this sheet.</div>';
      return;
    }

    const sheetName = data.title || name || 'Sheet';
    currentSheet    = sheetName;
    sheetText       = rows.map(r => r.join('\t')).join('\n');

    document.getElementById('sheetBreadcrumb').textContent = sheetName;
    document.getElementById('dataTitle').textContent       = sheetName;
    document.getElementById('dataSheetRef').textContent    = 'Sheet1';

    renderTable(rows[0], rows.slice(1));
    document.getElementById('insightCards').innerHTML      = '';
    document.getElementById('aiChatMessages').innerHTML   = `
      <div class="ai-msg ai-side">
        <div style="font-size:12.5px;line-height:1.6;">
          ✅ Loaded <strong>${esc(sheetName)}</strong> — ${rows.length} rows. Ask me anything about this data!
        </div>
      </div>`;
  } catch(e) {
    document.getElementById('dataTableWrap').innerHTML = `<div style="padding:20px"><div class="alert alert-error">Failed to load sheet. Check your connection.</div></div>`;
  }
}

// ── Refresh current view ──────────────────────────────────
function refreshData() {
  const d   = demoData[currentSheet];
  const idx = Object.keys(demoData).indexOf(currentSheet);
  if (d && idx >= 0) openDemo(currentSheet, idx);
}

// ── Load Google Drive file list ───────────────────────────
<?php if($gok): ?>
async function loadDrive() {
  try {
    const res  = await fetch('api/sheets.php?action=list');
    const data = await res.json();

    if (data.error) {
      document.getElementById('driveList').innerHTML = `<div style="font-size:12px;color:var(--red);padding:4px 0;">${esc(data.error)}</div>`;
      return;
    }

    if (!data.files || !data.files.length) {
      document.getElementById('driveList').innerHTML = '<div style="font-size:12px;color:var(--ink3);padding:4px 0;">No spreadsheets found in Drive.</div>';
      return;
    }

    document.getElementById('driveList').innerHTML = data.files.map(f => `
      <div class="file-item" onclick="loadSheetById('${esc(f.id)}', '${esc(f.name)}')">
        <div class="file-icon">
          <svg width="14" height="14" fill="none" stroke="var(--green)" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
        </div>
        <div style="min-width:0;">
          <div class="file-name">${esc(f.name)}</div>
          <div class="file-meta"><span>${esc(f.modified)}</span></div>
        </div>
      </div>`).join('');
  } catch(e) {
    document.getElementById('driveList').innerHTML = '<div style="font-size:12px;color:var(--red);padding:4px 0;">Failed to load Drive. Check Google connection.</div>';
  }
}
// Run Drive load when page is ready
document.addEventListener('DOMContentLoaded', loadDrive);
<?php endif; ?>

// ── Escape helper ──────────────────────────────────────────
function esc(s) {
  return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ── Init ───────────────────────────────────────────────────
sheetText = <?= json_encode(
  array_merge(
    [$demoData['Q4 Revenue Analysis']['headers']],
    $demoData['Q4 Revenue Analysis']['rows']
  )
) ?>.map(r => r.join('\t')).join('\n');
</script>

<style>
@keyframes bounce{0%,80%,100%{transform:scale(.5);opacity:.4}40%{transform:scale(1);opacity:1}}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
