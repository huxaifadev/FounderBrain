<?php
$user     = currentUser();
$uname    = htmlspecialchars($user['name'] ?? 'Huzaifa');
$uemail   = htmlspecialchars($user['email'] ?? '');
$upic     = htmlspecialchars($user['picture'] ?? '');
$initials = strtoupper(substr($user['name'] ?? 'H', 0, 2));
$page     = basename($_SERVER['PHP_SELF']);
$gok      = hasGoogleAuth();
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title><?= htmlspecialchars($pageTitle ?? 'FounderBrain') ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/app.css">
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-name">FounderBrain</div>
    <div class="logo-sub">Executive Suite</div>
  </div>

  <nav class="nav">
    <a href="<?= BASE_URL ?>/index.php" class="nav-item <?= $page==='index.php'?'active':'' ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>
    <a href="<?= BASE_URL ?>/chat.php" class="nav-item <?= $page==='chat.php'?'active':'' ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      Command Center
    </a>
    <a href="<?= BASE_URL ?>/sheets.php" class="nav-item <?= $page==='sheets.php'?'active':'' ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
      Sheets Intel
      <?php if(!$gok): ?><span style="margin-left:auto;background:var(--red);color:white;font-size:9px;font-weight:700;padding:1px 5px;border-radius:3px;">!</span><?php endif; ?>
    </a>
    <a href="<?= BASE_URL ?>/gmail.php" class="nav-item <?= $page==='gmail.php'?'active':'' ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      Gmail Analysis
      <?php if(!$gok): ?><span style="margin-left:auto;background:var(--red);color:white;font-size:9px;font-weight:700;padding:1px 5px;border-radius:3px;">!</span><?php endif; ?>
    </a>

    <div class="nav-divider"></div>

    <a href="<?= BASE_URL ?>/tasks.php" class="nav-item <?= $page==='tasks.php'?'active':'' ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
      Open Loops
    </a>
    <a href="<?= BASE_URL ?>/pitch.php" class="nav-item <?= $page==='pitch.php'?'active':'' ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
      Pitch Prep
    </a>
    <a href="<?= BASE_URL ?>/briefing.php" class="nav-item <?= $page==='briefing.php'?'active':'' ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Executive Insights
    </a>

    <div class="nav-divider"></div>

    <a href="<?= BASE_URL ?>/settings.php" class="nav-item <?= $page==='settings.php'?'active':'' ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
      Settings
    </a>
  </nav>

  <div class="sidebar-support">
    <a href="<?= BASE_URL ?>/settings.php">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
      Support
    </a>
    <a href="<?= BASE_URL ?>/auth/logout.php">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Logout
    </a>
  </div>

  <a href="<?= BASE_URL ?>/chat.php" class="sidebar-cta">
    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    New Initiative
  </a>

  <div class="sidebar-user">
    <div class="user-av">
      <?php if($upic): ?><img src="<?= $upic ?>" alt=""/><?php else: ?><?= $initials ?><?php endif; ?>
    </div>
    <div>
      <div class="user-nm"><?= $uname ?></div>
      <div class="user-role"><?= $gok ? 'Founder &amp; CEO' : 'Founder Mode' ?></div>
    </div>
  </div>
</aside>

<main class="main">
