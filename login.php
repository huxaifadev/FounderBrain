<?php
require_once __DIR__ . '/config.php';
if (isLoggedIn()) { header('Location: '.BASE_URL.'/index.php'); exit; }
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>FounderBrain — Sign In</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--indigo:#4f46e5;--indigo2:#4338ca;--ink:#0f0f1a;--ink2:#374151;--ink3:#6b7280;--border:#e5e7eb;--bg:#f8f8fc;--red:#dc2626;}
body{font-family:'Inter',sans-serif;background:var(--bg);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;
  background-image:radial-gradient(ellipse at 15% 50%,rgba(79,70,229,.07) 0%,transparent 55%),radial-gradient(ellipse at 85% 20%,rgba(79,70,229,.05) 0%,transparent 50%);}
body::before{content:'';position:fixed;inset:0;pointer-events:none;
  background-image:linear-gradient(rgba(229,231,235,.5) 1px,transparent 1px),linear-gradient(90deg,rgba(229,231,235,.5) 1px,transparent 1px);
  background-size:40px 40px;}
.wrap{display:grid;grid-template-columns:1fr 1fr;max-width:860px;width:100%;background:#fff;border-radius:16px;border:1px solid var(--border);box-shadow:0 8px 48px rgba(0,0,0,.08);overflow:hidden;position:relative;z-index:1;animation:up .4s cubic-bezier(.22,1,.36,1);}
@keyframes up{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}
.left{background:var(--indigo);padding:42px 38px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;}
.left::before{content:'';position:absolute;top:-70px;right:-70px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.08);}
.left::after{content:'';position:absolute;bottom:-50px;left:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,.05);}
.brand-name{font-size:16px;font-weight:800;color:#fff;letter-spacing:-.3px;position:relative;z-index:1;}
.brand-sub{font-size:10px;font-weight:600;color:rgba(255,255,255,.55);letter-spacing:.1em;text-transform:uppercase;margin-top:2px;position:relative;z-index:1;}
.hero{position:relative;z-index:1;}
.hero h2{font-size:28px;font-weight:800;color:#fff;line-height:1.2;letter-spacing:-.5px;margin-bottom:12px;}
.hero p{font-size:13px;color:rgba(255,255,255,.7);line-height:1.7;}
.feats{display:flex;flex-direction:column;gap:9px;position:relative;z-index:1;}
.feat{display:flex;align-items:center;gap:8px;font-size:12.5px;color:rgba(255,255,255,.8);}
.feat-dot{width:5px;height:5px;border-radius:50%;background:rgba(255,255,255,.55);flex-shrink:0;}
.right{padding:42px 38px;display:flex;flex-direction:column;justify-content:center;}
.eyebrow{display:inline-flex;align-items:center;gap:5px;background:#eef2ff;color:var(--indigo);border:1px solid #e0e7ff;border-radius:20px;padding:4px 12px;font-size:10.5px;font-weight:600;letter-spacing:.05em;margin-bottom:16px;}
.right h1{font-size:22px;font-weight:800;color:var(--ink);letter-spacing:-.4px;margin-bottom:5px;}
.right .sub{font-size:13px;color:var(--ink3);margin-bottom:24px;}
.err{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 13px;font-size:12.5px;color:var(--red);margin-bottom:14px;}
.field{margin-bottom:12px;}
.field label{display:block;font-size:11px;font-weight:700;color:var(--ink2);letter-spacing:.06em;text-transform:uppercase;margin-bottom:5px;}
.field input{width:100%;border:1.5px solid var(--border);border-radius:8px;padding:10px 12px;font-family:'Inter',sans-serif;font-size:13.5px;color:var(--ink);background:var(--bg);outline:none;transition:border-color .12s;}
.field input:focus{border-color:var(--indigo);background:#fff;}
.field input::placeholder{color:#9ca3af;}
.btn-sub{width:100%;background:var(--indigo);color:#fff;border:none;border-radius:8px;padding:12px;font-family:'Inter',sans-serif;font-size:14px;font-weight:700;cursor:pointer;transition:background .12s;margin-bottom:14px;}
.btn-sub:hover{background:var(--indigo2);}
.divider{display:flex;align-items:center;gap:12px;color:#9ca3af;font-size:12px;margin-bottom:14px;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border);}
.btn-google{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:10px;background:#fff;border:1.5px solid var(--border);border-radius:8px;font-family:'Inter',sans-serif;font-size:13.5px;font-weight:500;color:var(--ink);cursor:pointer;text-decoration:none;transition:all .12s;box-shadow:0 1px 2px rgba(0,0,0,.04);}
.btn-google:hover{border-color:#4285f4;box-shadow:0 3px 10px rgba(66,133,244,.15);}
.hint{text-align:center;font-size:12px;color:#9ca3af;margin-top:16px;}
.hint strong{color:var(--ink2);}
@media(max-width:620px){.wrap{grid-template-columns:1fr}.left{padding:28px;min-height:auto}.right{padding:28px}}
</style>
</head>
<body>
<div class="wrap">
  <div class="left">
    <div><div class="brand-name">FounderBrain</div><div class="brand-sub">Executive Suite</div></div>
    <div class="hero">
      <h2>Good morning,<br>Founder.<br>I've triaged<br>your world.</h2>
      <p>Stop losing 2+ hours a day to context-switching, forgotten follow-ups, and scattered tools.</p>
    </div>
    <div class="feats">
      <div class="feat"><div class="feat-dot"></div>AI reads &amp; drafts your Gmail replies</div>
      <div class="feat"><div class="feat-dot"></div>Auto follow-up engine — never miss a lead</div>
      <div class="feat"><div class="feat-dot"></div>Live Google Sheets analysis</div>
      <div class="feat"><div class="feat-dot"></div>Brain dump → prioritized task list</div>
      <div class="feat"><div class="feat-dot"></div>YC interview &amp; investor pitch coach</div>
    </div>
  </div>
  <div class="right">
    <div class="eyebrow">✦ GDG Build with AI · Hackathon 2026</div>
    <h1>Welcome back</h1>
    <p class="sub">Sign in to your executive workspace.</p>
    <?php if($error): ?><div class="err">⚠️ <?=htmlspecialchars($error)?></div><?php endif; ?>
    <form method="POST" action="auth/login_action.php">
      <div class="field"><label>Username</label><input type="text" name="username" placeholder="huzaifa" required autocomplete="username"/></div>
      <div class="field"><label>Password</label><input type="password" name="password" placeholder="••••••••" required/></div>
      <button type="submit" class="btn-sub">Sign In →</button>
    </form>
    <div class="divider">or</div>
    <a href="auth/google.php" class="btn-google">
      <svg width="17" height="17" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
      Continue with Google
    </a>
    <p class="hint">Default: <strong>huzaifa</strong> / <strong>12345678</strong></p>
  </div>
</div>
</body></html>
