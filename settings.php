<?php
require_once __DIR__ . '/config.php';
requireLogin();
$pageTitle = 'Settings — FounderBrain';
$user = currentUser();
$gok  = hasGoogleAuth();
include __DIR__ . '/includes/header.php';
?>
<div class="topbar">
  <div class="topbar-breadcrumb">
    <span class="crumb-main" style="color:var(--ink3);font-weight:400;">Settings</span>
    <span class="crumb-sep">/</span>
    <span class="crumb-sub">Configuration</span>
  </div>
  <div class="topbar-search" style="margin-left:auto;">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="color:var(--ink4)"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    <input placeholder="Search settings..."/>
  </div>
  <div class="topbar-actions">
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--ink);">
      <div class="user-av" style="width:28px;height:28px;font-size:11px;">
        <?php if (!empty($user['picture'])): ?><img src="<?= htmlspecialchars($user['picture']) ?>" style="width:28px;height:28px;border-radius:50%;object-fit:cover;" alt=""/><?php else: ?><?= strtoupper(substr($user['name'] ?? 'H', 0, 2)) ?><?php endif; ?>
      </div>
      <?= htmlspecialchars($user['name'] ?? 'Huzaifa') ?>
    </div>
  </div>
</div>

<div class="page" style="max-width:700px;">
  <div style="font-size:28px;font-weight:800;color:var(--ink);letter-spacing:-.5px;margin-bottom:5px;">General Settings</div>
  <div style="font-size:13.5px;color:var(--ink3);margin-bottom:24px;">Configure your executive workspace and intelligence parameters.</div>

  <!-- Account -->
  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;margin-bottom:16px;">
    <div style="padding:16px 22px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
      <div style="font-size:16px;font-weight:800;color:var(--ink);display:flex;align-items:center;gap:9px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Account Settings
      </div>
    </div>
    <div style="padding:20px 22px;">
      <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;padding-bottom:20px;border-bottom:1px solid var(--border);">
        <?php if (!empty($user['picture'])): ?>
        <img src="<?= htmlspecialchars($user['picture']) ?>" style="width:52px;height:52px;border-radius:12px;object-fit:cover;" alt=""/>
        <?php else: ?>
        <div style="width:52px;height:52px;border-radius:12px;background:var(--indigo);color:white;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;"><?= strtoupper(substr($user['name'] ?? 'H', 0, 2)) ?></div>
        <?php endif; ?>
        <div>
          <div style="font-size:16px;font-weight:700;color:var(--ink);"><?= htmlspecialchars($user['name'] ?? 'Huzaifa') ?></div>
          <div style="font-size:13px;color:var(--ink3);"><?= htmlspecialchars($user['email'] ?? 'huzaifa@founderbrain.chat') ?></div>
        </div>
      </div>

      <div style="font-size:10.5px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px;">Connected Services</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:<?= $gok ? '0' : '14px' ?>;">
        <div style="display:flex;align-items:center;gap:10px;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius);">
          <div style="width:28px;height:28px;border-radius:6px;background:#fff0f0;display:flex;align-items:center;justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" fill="#ea4335" opacity=".2"/><polyline points="22,6 12,13 2,6" stroke="#ea4335" stroke-width="1.5"/></svg>
          </div>
          <span style="font-size:13px;font-weight:500;color:var(--ink);">Google Gmail</span>
          <div style="width:7px;height:7px;border-radius:50%;background:<?= $gok ? 'var(--green)' : 'var(--border2)' ?>;margin-left:auto;"></div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;padding:10px 13px;border:1px solid var(--border);border-radius:var(--radius);">
          <div style="width:28px;height:28px;border-radius:6px;background:#f0fff4;display:flex;align-items:center;justify-content:center;">
            <svg width="14" height="14" fill="none" stroke="var(--green)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
          </div>
          <span style="font-size:13px;font-weight:500;color:var(--ink);">Google Sheets</span>
          <div style="width:7px;height:7px;border-radius:50%;background:<?= $gok ? 'var(--green)' : 'var(--border2)' ?>;margin-left:auto;"></div>
        </div>
      </div>
      <?php if (!$gok): ?>
      <a href="<?= BASE_URL ?>/auth/google.php" class="btn btn-indigo btn-sm" style="margin-top:4px;">
        <svg width="13" height="13" viewBox="0 0 24 24"><path fill="#fff" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/></svg>
        Connect Google Account
      </a>
      <?php else: ?>
      <div style="display:flex;align-items:center;gap:7px;margin-top:10px;font-size:12.5px;font-weight:500;color:var(--green);">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        Google account fully connected — Gmail &amp; Sheets active
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- AI Engine -->
  <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--radius-xl);overflow:hidden;margin-bottom:16px;">
    <div style="padding:16px 22px;border-bottom:1px solid var(--border);">
      <div style="font-size:16px;font-weight:800;color:var(--ink);display:flex;align-items:center;gap:9px;">
        <svg width="16" height="16" fill="none" stroke="var(--indigo)" stroke-width="2" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
        AI Intelligence Engine
      </div>
    </div>
    <div style="padding:20px 22px;">
      <div style="font-size:10.5px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.07em;margin-bottom:6px;">Primary Model</div>
      <select style="width:100%;border:1.5px solid var(--border);border-radius:var(--radius);padding:9px 12px;font-size:13px;font-family:Inter,sans-serif;color:var(--ink);outline:none;margin-bottom:14px;">
        <option selected>Gemini 1.5 Flash</option>
        <option>Gemini 2.0 Flash</option>
        <option>Gemini 1.5 Pro</option>
      </select>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg);border-radius:var(--radius);">
        <div>
          <div style="font-size:10px;font-weight:700;color:var(--ink4);text-transform:uppercase;letter-spacing:.08em;margin-bottom:3px;">Provider</div>
          <div style="font-size:14px;font-weight:700;color:var(--ink);">Google AI Studio</div>
        </div>
        <span style="background:var(--greenbg);color:var(--green);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">● Active</span>
      </div>
    </div>
  </div>

  <!-- Footer links -->
  <div style="display:flex;align-items:center;gap:20px;padding-top:16px;font-size:12px;color:var(--ink4);flex-wrap:wrap;">
    <span style="font-weight:700;color:var(--ink);">FounderBrain</span>
    <span>v2.4.0-stable</span>
    <a href="#" style="color:var(--ink3);text-decoration:none;">Privacy Policy</a>
    <a href="#" style="color:var(--ink3);text-decoration:none;">Terms of Service</a>
    <a href="#" style="color:var(--ink3);text-decoration:none;">Documentation</a>
    <span>Engineered for High-Performance Founders.</span>
    <a href="<?= BASE_URL ?>/auth/logout.php" style="margin-left:auto;color:var(--red);font-weight:600;text-decoration:none;">Logout →</a>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
