<?php
// Security-Header via PHP. OVHcloud Free Hosting hat kein mod_headers, darum hier.
// HSTS wird bewusst nicht via PHP gesetzt.
header_remove('X-Powered-By');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), camera=(), microphone=(), payment=(), usb=()');
header('X-XSS-Protection: 0');
header(
    "Content-Security-Policy: "
    . "default-src 'self'; "
    . "script-src 'self' 'unsafe-inline' https://js.hcaptcha.com https://newassets.hcaptcha.com; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
    . "font-src 'self' https://fonts.gstatic.com; "
    . "frame-src https://newassets.hcaptcha.com; "
    . "connect-src 'self' https://api.hcaptcha.com; "
    . "img-src 'self' data:; "
    . "object-src 'none'; "
    . "base-uri 'self'; "
    . "form-action 'self'"
);

$config   = require __DIR__ . '/config.php';
$projects = require __DIR__ . '/projects.php';
$firma    = $config['firma'];
$seo      = $config['seo'];
$hckey    = $config['hcaptcha']['site_key'];

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$allowed = ['home', 'impressum', 'datenschutz'];
if (!in_array($page, $allowed)) $page = 'home';

// Hilfsfunktion: Platzhalter-SVG als Data-URI
function placeholder_svg(string $text, string $bg = '#e4e4e0', string $fg = '#9a9a94'): string {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="750">'
         . '<rect width="1200" height="750" fill="' . $bg . '"/>'
         . '<text x="600" y="390" font-family="sans-serif" font-size="28" fill="' . $fg . '" text-anchor="middle">' . htmlspecialchars($text) . '</text>'
         . '</svg>';
    return 'data:image/svg+xml;base64,' . base64_encode($svg);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" href="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2032%2032'%3E%3Crect%20width='32'%20height='32'%20rx='4'%20fill='%233CB975'/%3E%3C/svg%3E">
<title><?php
    if ($page === 'impressum')    echo 'Impressum | Wittwer Informatik';
    elseif ($page === 'datenschutz') echo 'Datenschutz | Wittwer Informatik';
    else echo htmlspecialchars($seo['title']);
?></title>
<meta name="description" content="<?php echo htmlspecialchars($seo['description']); ?>">
<meta name="author" content="<?php echo htmlspecialchars($firma['inhaber'] . ', ' . $firma['name']); ?>">
<link rel="canonical" href="<?php echo htmlspecialchars($seo['url']); ?>/">
<meta property="og:title" content="<?php echo htmlspecialchars($seo['title']); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($seo['description']); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo htmlspecialchars($seo['url']); ?>/">
<meta property="og:locale" content="de_CH">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "<?php echo $firma['name']; ?>",
  "description": "IT-Beratung und Softwareentwicklung für KMU und Vereine in der Schweiz.",
  "url": "<?php echo $firma['website']; ?>",
  "email": "<?php echo $firma['mail']; ?>",
  "founder": {"@type": "Person", "name": "<?php echo $firma['inhaber']; ?>"},
  "address": {"@type": "PostalAddress", "addressRegion": "BE", "addressCountry": "CH"},
  "areaServed": "CH",
  "knowsLanguage": ["de", "en"]
}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
<?php if ($page === 'home' && !empty($hckey)): ?>
<script src="https://js.hcaptcha.com/1/api.js" async defer></script>
<?php endif; ?>
<style>
:root {
  --green: #3CB975;
  --ink: #181818;
  --mid: #5a5a5a;
  --faint: #e2e2de;
  --bg: #f7f7f5;
  --white: #ffffff;
}
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { font-size: 16px; scroll-behavior: smooth; }
body {
  font-family: 'Inter', system-ui, sans-serif;
  background: var(--bg);
  color: var(--ink);
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
}
nav {
  position: sticky; top: 0; z-index: 100;
  background: var(--bg);
  border-bottom: 1px solid var(--faint);
  height: 58px; display: flex; align-items: center;
}
.nav-inner {
  max-width: 1100px; margin: 0 auto;
  padding: 0 clamp(1.25rem, 5vw, 3rem);
  width: 100%;
  display: flex; align-items: center; justify-content: space-between;
}
.nav-logo { height: 40px; width: auto; display: block; }
.nav-links { display: flex; gap: 2rem; list-style: none; }
.nav-burger { display: none; }
.nav-links a {
  font-size: 0.875rem; font-weight: 500;
  color: var(--mid); text-decoration: none; transition: color 0.15s;
}
.nav-links a:hover { color: var(--ink); }
.wrap { max-width: 1100px; margin: 0 auto; padding: 0 clamp(1.25rem, 5vw, 3rem); }
.hero { padding: 6rem 0 5rem; max-width: 660px; }
h1 {
  font-family: 'Ubuntu', sans-serif;
  font-size: clamp(2.2rem, 5.5vw, 3.6rem);
  font-weight: 700; line-height: 1.12;
  letter-spacing: -0.02em; color: var(--ink); margin-bottom: 1.25rem;
}
h1 em { font-style: normal; color: var(--green); }
.hero-sub {
  font-size: 1.0625rem; color: var(--mid);
  max-width: 520px; line-height: 1.7; margin-bottom: 2.25rem;
}
.btn {
  display: inline-block;
  background: var(--ink); color: var(--bg);
  font-family: 'Inter', sans-serif;
  font-size: 0.875rem; font-weight: 500;
  padding: 0.7rem 1.6rem; text-decoration: none;
  border: 2px solid var(--ink); transition: background 0.15s, color 0.15s;
}
.btn:hover { background: transparent; color: var(--ink); }
hr.div { border: none; border-top: 1px solid var(--faint); }
section { padding: 4.5rem 0; }
.sec-label {
  font-size: 0.75rem; font-weight: 500;
  color: var(--green); letter-spacing: 0.08em; margin-bottom: 0.5rem;
}
h2 {
  font-family: 'Ubuntu', sans-serif;
  font-size: clamp(1.4rem, 3vw, 2rem);
  font-weight: 700; color: var(--ink);
  line-height: 1.2; letter-spacing: -0.01em; margin-bottom: 0.75rem;
}
.sec-intro {
  font-size: 0.9375rem; color: var(--mid);
  max-width: 540px; line-height: 1.7; margin-bottom: 3rem;
}
.steps { border: 1px solid var(--faint); }
.step {
  display: flex; gap: 1.5rem;
  padding: 1.5rem 1.75rem;
  border-bottom: 1px solid var(--faint); align-items: flex-start;
}
.step:last-child { border-bottom: none; }
.step-num { font-size: 0.8125rem; font-weight: 700; color: var(--green); min-width: 20px; padding-top: 3px; }
.step h3 {
  font-family: 'Ubuntu', sans-serif;
  font-size: 0.9375rem; font-weight: 700; color: var(--ink); margin-bottom: 0.35rem;
}
.step p { font-size: 0.875rem; color: var(--mid); line-height: 1.65; }
.tool-tags { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.75rem; }
.tool-tag {
  font-size: 0.6875rem; font-weight: 500;
  color: var(--green); border: 1px solid var(--green); padding: 0.15rem 0.5rem;
}
.examples {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 1px; background: var(--faint); border: 1px solid var(--faint);
}
.ex { background: var(--bg); padding: 1.75rem; }
.ex-icon {
  width: 28px; height: 28px; margin-bottom: 1rem; color: var(--green);
  stroke: currentColor; fill: none;
  stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round;
}
.ex h3 {
  font-family: 'Ubuntu', sans-serif;
  font-size: 0.9375rem; font-weight: 700; color: var(--ink); margin-bottom: 0.4rem;
}
.ex p { font-size: 0.875rem; color: var(--mid); line-height: 1.65; }

/* PROJECTS */
.projects {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.25rem;
}
.proj {
  border: 1px solid var(--faint); padding: 1.5rem;
  background: var(--white); transition: border-color 0.2s;
  cursor: pointer;
}
.proj:hover { border-color: var(--green); }
.proj.dim { opacity: 0.35; pointer-events: none; cursor: default; }
.proj-tag {
  font-size: 0.6875rem; font-weight: 500;
  color: var(--green); border: 1px solid var(--green);
  padding: 0.15rem 0.45rem; display: inline-block; margin-bottom: 0.85rem;
}
.proj h3 {
  font-family: 'Ubuntu', sans-serif;
  font-size: 1rem; font-weight: 700; color: var(--ink); margin-bottom: 0.4rem;
}
.proj p { font-size: 0.875rem; color: var(--mid); line-height: 1.6; margin-bottom: 1rem; }
.proj-mehr {
  font-size: 0.8125rem; font-weight: 500; color: var(--green);
}

/* MODAL */
.modal-overlay {
  display: none; position: fixed; inset: 0; z-index: 500;
  background: rgba(0,0,0,0.55); align-items: center; justify-content: center;
  padding: 1.5rem;
}
.modal-overlay.open { display: flex; }
.modal {
  background: var(--white); max-width: 720px; width: 100%;
  max-height: 90vh; overflow-y: auto;
  position: relative;
}
.modal-close {
  position: sticky; top: 0; left: 100%;
  background: var(--white); border: none;
  font-size: 1.25rem; color: var(--mid);
  cursor: pointer; padding: 0.75rem 1rem;
  line-height: 1; float: right;
}
.modal-close:hover { color: var(--ink); }
.modal-img-main {
  width: 100%; aspect-ratio: 16/10;
  object-fit: cover; display: block;
  background: var(--faint);
}
.modal-thumbs {
  display: flex; gap: 0.5rem; padding: 0.75rem 1.5rem 0;
}
.modal-thumb {
  width: 72px; height: 46px; object-fit: cover;
  cursor: pointer; border: 2px solid transparent;
  opacity: 0.65; transition: opacity 0.15s, border-color 0.15s;
  background: var(--faint);
}
.modal-thumb.active { border-color: var(--green); opacity: 1; }
.modal-body { padding: 1.5rem; }
.modal-body h3 {
  font-family: 'Ubuntu', sans-serif;
  font-size: 1.375rem; font-weight: 700; color: var(--ink); margin-bottom: 0.75rem;
}
.modal-body p { font-size: 0.9375rem; color: var(--mid); line-height: 1.75; margin-bottom: 1.25rem; }
.modal-features { list-style: none; margin-bottom: 1.25rem; }
.modal-features li {
  font-size: 0.875rem; color: var(--mid);
  padding: 0.35rem 0; border-bottom: 1px solid var(--faint);
  display: flex; align-items: flex-start; gap: 0.6rem;
}
.modal-features li::before { content: ''; display: inline-block; width: 6px; height: 6px; background: var(--green); margin-top: 0.45rem; flex-shrink: 0; }
.modal-stack { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
.modal-stack span {
  font-size: 0.6875rem; font-weight: 500;
  color: var(--mid); border: 1px solid var(--faint); padding: 0.15rem 0.5rem;
}
.modal-link {
  display: inline-block;
  background: var(--ink); color: var(--bg);
  font-size: 0.875rem; font-weight: 500;
  padding: 0.65rem 1.5rem; text-decoration: none;
  border: 2px solid var(--ink); transition: background 0.15s, color 0.15s;
}
.modal-link:hover { background: transparent; color: var(--ink); }

/* ABOUT */
.about-grid {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 4rem; align-items: start;
}
.about-text p {
  font-size: 0.9375rem; color: var(--mid);
  line-height: 1.8; margin-bottom: 1.1rem;
}
.about-text p:last-child { margin: 0; }
.about-text strong { color: var(--ink); font-weight: 500; }
.facts { border: 1px solid var(--faint); }
.fact {
  display: flex; justify-content: space-between;
  align-items: baseline; gap: 1rem;
  padding: 1rem 1.25rem; border-bottom: 1px solid var(--faint);
}
.fact:last-child { border-bottom: none; }
.fact-l { font-size: 0.8125rem; color: var(--mid); }
.fact-r {
  font-family: 'Ubuntu', sans-serif;
  font-size: 0.9375rem; font-weight: 700; color: var(--ink); text-align: right;
}

/* KONTAKT */
.contact-band { background: var(--ink); padding: 5rem 0; }
.contact-band .sec-label { color: var(--green); }
.contact-band h2 { color: var(--bg); margin-bottom: 0.75rem; }
.contact-band .sec-intro { color: rgba(255,255,255,0.45); margin-bottom: 2rem; }

/* FORMULAR */
.contact-form { max-width: 520px; display: flex; flex-direction: column; gap: 1rem; }
.form-group { display: flex; flex-direction: column; gap: 0.35rem; }
.form-group label { font-size: 0.8125rem; font-weight: 500; color: rgba(255,255,255,0.6); }
.form-group input,
.form-group textarea {
  background: rgba(255,255,255,0.07);
  border: 1px solid rgba(255,255,255,0.15);
  color: var(--bg);
  font-family: 'Inter', sans-serif;
  font-size: 0.9375rem;
  padding: 0.7rem 0.9rem;
  outline: none;
  transition: border-color 0.15s;
  width: 100%;
}
.form-group input::placeholder,
.form-group textarea::placeholder { color: rgba(255,255,255,0.25); }
.form-group input:focus,
.form-group textarea:focus { border-color: var(--green); }
.form-group textarea { resize: vertical; min-height: 130px; }
.form-error { font-size: 0.8125rem; color: #f87171; margin-top: 0.25rem; display: none; }
.form-msg {
  font-size: 0.875rem; padding: 0.75rem 1rem;
  display: none; margin-top: 0.5rem;
}
.form-msg.ok { background: rgba(60,185,117,0.15); color: var(--green); }
.form-msg.err { background: rgba(248,113,113,0.15); color: #f87171; }
.btn-submit {
  background: var(--green); color: var(--ink);
  font-family: 'Inter', sans-serif;
  font-size: 0.875rem; font-weight: 500;
  padding: 0.7rem 1.6rem;
  border: 2px solid var(--green);
  cursor: pointer; transition: opacity 0.15s;
  align-self: flex-start;
}
.btn-submit:hover { opacity: 0.85; }
.btn-submit:disabled { opacity: 0.5; cursor: not-allowed; }

/* FOOTER */
.footer-inner {
  display: flex; justify-content: space-between;
  align-items: center; flex-wrap: wrap; gap: 0.75rem;
  padding: 1.75rem 0; border-top: 1px solid var(--faint);
}
.footer-inner p, .footer-inner a {
  font-size: 0.8125rem; color: var(--mid); text-decoration: none;
}
.footer-inner a:hover { color: var(--ink); }
.footer-links { display: flex; gap: 1.5rem; }

/* LEGAL */
.legal { padding: 5rem 0; max-width: 680px; }
.legal h1 { font-size: clamp(1.75rem, 4vw, 2.5rem); margin-bottom: 2rem; }
.legal h2 { font-size: 1.1rem; margin-bottom: 0.75rem; margin-top: 2.5rem; }
.legal h2:first-of-type { margin-top: 0; }
.legal p { font-size: 0.9375rem; color: var(--mid); line-height: 1.75; margin-bottom: 0.75rem; }
.legal ul { margin-left: 1.25rem; margin-top: 0.5rem; margin-bottom: 0.75rem; }
.legal li { font-size: 0.9375rem; color: var(--mid); line-height: 2; }
.legal a { color: var(--green); }

@media (max-width: 720px) {
  /* Nav über den Backdrop heben, sonst liegt das Slide-in dahinter */
  nav { z-index: 200; }
  .nav-burger {
    display: flex; flex-direction: column; justify-content: center;
    gap: 5px; position: fixed; z-index: 210;
    top: 9px; right: clamp(1.25rem, 5vw, 3rem);
    width: 40px; height: 40px; padding: 8px;
    background: none; border: none; cursor: pointer;
  }
  .nav-burger span {
    display: block; width: 100%; height: 2px;
    background: var(--ink); border-radius: 2px;
    transition: transform 0.25s, opacity 0.2s;
  }
  .nav-burger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
  .nav-burger.open span:nth-child(2) { opacity: 0; }
  .nav-burger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
  .nav-links {
    position: fixed; top: 0; right: 0; z-index: 200;
    flex-direction: column; align-items: stretch; gap: 0;
    width: min(78vw, 300px); height: 100vh;
    padding: 4.75rem 2rem 2rem;
    background: var(--white); border-left: 1px solid var(--faint);
    transform: translateX(100%); transition: transform 0.28s ease;
  }
  .nav-links.open { transform: translateX(0); }
  .nav-links a {
    display: block; padding: 0.9rem 0; font-size: 1rem;
    border-bottom: 1px solid var(--faint);
  }
  .nav-backdrop {
    display: none; position: fixed; inset: 0; z-index: 150;
    background: rgba(0,0,0,0.4);
  }
  .nav-backdrop.open { display: block; }
  .about-grid { grid-template-columns: 1fr; gap: 2rem; }
  .hero { padding: 3.5rem 0 3rem; }
  .modal-thumbs { padding: 0.5rem 1rem 0; }
  .modal-body { padding: 1.25rem; }
}
</style>
</head>
<body>

<nav>
  <div class="nav-inner">
    <a href="/" aria-label="Wittwer Informatik Startseite">
      <img class="nav-logo" src="/assets/images/logos/logo_transparent_250x64.png" width="250" height="64" alt="Wittwer Informatik">
    </a>
    <ul class="nav-links" id="nav-links">
      <li><a href="/#wie">Wie ich helfe</a></li>
      <li><a href="/#beispiele">Beispiele</a></li>
      <li><a href="/#projekte">Projekte</a></li>
      <li><a href="/#kontakt">Kontakt</a></li>
    </ul>
  </div>
</nav>
<button class="nav-burger" id="nav-burger" type="button" aria-label="Menü öffnen" aria-expanded="false" aria-controls="nav-links">
  <span></span><span></span><span></span>
</button>
<div class="nav-backdrop" id="nav-backdrop"></div>

<?php if ($page === 'home'): ?>

<div class="wrap">
  <div class="hero">
    <h1>Du hast ein Problem.<br>Ich find die <em>Lösung.</em></h1>
    <p class="hero-sub">Zuerst schaue ich was vorhanden ist. Dann wählen wir gemeinsam das Richtige, ob das eine bestehende Lösung ist oder eine Eigenentwicklung. Von Anfang bis Ende.</p>
    <a href="#kontakt" class="btn">Schreib mir</a>
  </div>
</div>

<hr class="div">

<section id="wie">
  <div class="wrap">
    <div class="sec-label">Wie ich helfe</div>
    <h2>Erst verstehen, dann lösen</h2>
    <p class="sec-intro">Ich verkaufe kein bestimmtes Werkzeug. Ich löse dein Problem mit dem, was sinnvoll ist.</p>
    <div class="steps">
      <div class="step">
        <div class="step-num">1</div>
        <div>
          <h3>Problem verstehen</h3>
          <p>Was nervt? Was kostet Zeit? Was soll modernisiert werden? Wir reden darüber, unkompliziert und ohne Fachchinesisch.</p>
        </div>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <div>
          <h3>Das Richtige wählen</h3>
          <p>Oft reicht was du schon hast. Manchmal ist eine Open-Source-Lösung die bessere Wahl. Manchmal braucht es eine Eigenentwicklung.</p>
          <div class="tool-tags">
            <span class="tool-tag">Microsoft 365</span>
            <span class="tool-tag">Open Source</span>
            <span class="tool-tag">Bestehende SaaS</span>
            <span class="tool-tag">Eigenentwicklung</span>
          </div>
        </div>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <div>
          <h3>Umsetzen und betreiben</h3>
          <p>Ich setze um, erkläre was ich gemacht habe, und bin danach noch da. Kein "deploy and forget".</p>
        </div>
      </div>
    </div>
  </div>
</section>

<hr class="div">

<section id="beispiele">
  <div class="wrap">
    <div class="sec-label">Typische Situationen</div>
    <h2>Kommt dir das bekannt vor?</h2>
    <p class="sec-intro">Keine Theorie. Das sind echte Probleme, die ich bereits gelöst habe.</p>
    <div class="examples">
      <div class="ex">
        <svg class="ex-icon" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M8 2v4M16 2v4M2 10h20"/></svg>
        <h3>M365 ist bezahlt, aber ihr nutzt nur Outlook</h3>
        <p>Teams, SharePoint, Power Automate, Forms. Alles schon drin. Ich richte es ein und zeige euch, wie es den Alltag vereinfacht.</p>
      </div>
      <div class="ex">
        <svg class="ex-icon" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6M9 16h4"/></svg>
        <h3>Mitgliederliste in Excel, Kommunikation über WhatsApp</h3>
        <p>Kein System, keine Übersicht. Ich helfe euch, das sauber aufzusetzen, ohne dass jemand eine Ausbildung braucht.</p>
      </div>
      <div class="ex">
        <svg class="ex-icon" viewBox="0 0 24 24"><path d="M5 17H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><path d="M13 16h4M15 14v4"/></svg>
        <h3>Fahrgemeinschaft für den Vereinsausflug organisieren</h3>
        <p>Mit Mitfahrli geht das ohne App-Download, ohne Anmeldung. Einfach Link teilen, fertig.</p>
      </div>
      <div class="ex">
        <svg class="ex-icon" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
        <h3>Prozess läuft noch auf Papier oder per Mail hin und her</h3>
        <p>Ich schaue mir an was es braucht und schlage die einfachste Lösung vor, nicht die teuerste.</p>
      </div>
    </div>
  </div>
</section>

<hr class="div">

<section id="projekte">
  <div class="wrap">
    <div class="sec-label">Projekte</div>
    <h2>Was ich gebaut habe</h2>
    <p class="sec-intro">Eigene Produkte, die ich selbst betreibe.</p>
    <div class="projects">
      <?php foreach ($projects as $p): ?>
        <?php if (!empty($p['placeholder'])): ?>
          <div class="proj dim">
            <span class="proj-tag"><?php echo htmlspecialchars($p['status']); ?></span>
            <h3><?php echo htmlspecialchars($p['name']); ?></h3>
            <p><?php echo htmlspecialchars($p['kurz']); ?></p>
          </div>
        <?php else: ?>
          <div class="proj" onclick="openModal('<?php echo $p['slug']; ?>')" role="button" tabindex="0" aria-label="<?php echo htmlspecialchars($p['name']); ?> Details öffnen" onkeydown="if(event.key==='Enter')openModal('<?php echo $p['slug']; ?>')">
            <span class="proj-tag"><?php echo htmlspecialchars($p['tag'] . ' · ' . $p['status']); ?></span>
            <h3><?php echo htmlspecialchars($p['name']); ?></h3>
            <p><?php echo htmlspecialchars($p['kurz']); ?></p>
            <span class="proj-mehr">Details ansehen</span>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
// Modal-Daten als JSON für JavaScript
$modal_data = [];
foreach ($projects as $p) {
    if (!empty($p['placeholder'])) continue;
    $bilder = $p['bilder'];
    // Platzhalter wenn keine Bilder vorhanden
    if (empty($bilder)) {
        $bilder = [placeholder_svg($p['name'])];
    }
    $modal_data[$p['slug']] = [
        'name'        => $p['name'],
        'beschreibung' => $p['beschreibung'],
        'features'    => $p['features'],
        'stack'       => $p['stack'],
        'url'         => $p['url'],
        'url_label'   => $p['url_label'],
        'bilder'      => $bilder,
    ];
}
?>

<!-- MODALS -->
<div class="modal-overlay" id="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div class="modal" id="modal-box">
    <button class="modal-close" onclick="closeModal()" aria-label="Schliessen">&#x2715;</button>
    <img class="modal-img-main" id="modal-img-main" src="" alt="">
    <div class="modal-thumbs" id="modal-thumbs"></div>
    <div class="modal-body">
      <h3 id="modal-title"></h3>
      <p id="modal-beschreibung"></p>
      <ul class="modal-features" id="modal-features"></ul>
      <div class="modal-stack" id="modal-stack"></div>
      <a id="modal-link" href="#" target="_blank" rel="noopener" class="modal-link"></a>
    </div>
  </div>
</div>

<hr class="div">

<section id="ueber">
  <div class="wrap">
    <div class="sec-label">Über mich</div>
    <h2><?php echo htmlspecialchars($firma['inhaber']); ?></h2>
    <div class="about-grid">
      <div class="about-text">
        <p>Ich bin Einzelunternehmer aus dem <?php echo htmlspecialchars($firma['kanton']); ?>. Ich helfe kleinen KMU und Vereinen, ihre IT-Probleme zu lösen, pragmatisch, direkt und ohne unnötigen Aufwand.</p>
        <p>Ich bin kein grosses Unternehmen. Das ist Absicht. Du redest immer mit mir, nicht mit einem Projektmanager, der das dann weitergibt.</p>
        <p>Neben der Arbeit trainiere ich Juniorinnen und Junioren im Fussball.</p>
      </div>
      <div class="facts">
        <div class="fact"><span class="fact-l">Standort</span><span class="fact-r"><?php echo htmlspecialchars($firma['kanton'] . ', ' . $firma['land']); ?></span></div>
        <div class="fact"><span class="fact-l">Unternehmensform</span><span class="fact-r">Einzelfirma</span></div>
        <div class="fact"><span class="fact-l">Zielgruppe</span><span class="fact-r">KMU und Vereine</span></div>
        <div class="fact"><span class="fact-l">Sprachen</span><span class="fact-r">DE, EN</span></div>
      </div>
    </div>
  </div>
</section>

<div class="contact-band" id="kontakt">
  <div class="wrap">
    <div class="sec-label">Kontakt</div>
    <h2>Beschäftigt dich etwas?</h2>
    <p class="sec-intro">Schreib mir kurz was dich beschäftigt. Ich melde mich in der Regel innert einem Werktag.</p>

    <form class="contact-form" id="contact-form" novalidate>
      <div class="form-group">
        <label for="cf-name">Dein Name</label>
        <input type="text" id="cf-name" name="name" placeholder="Max Muster" autocomplete="name">
        <span class="form-error" id="err-name"></span>
      </div>
      <div class="form-group">
        <label for="cf-mail">Deine E-Mail</label>
        <input type="email" id="cf-mail" name="mail" placeholder="max@beispiel.ch" autocomplete="email">
        <span class="form-error" id="err-mail"></span>
      </div>
      <div class="form-group">
        <label for="cf-nachricht">Deine Nachricht</label>
        <textarea id="cf-nachricht" name="nachricht" placeholder="Worum geht es?"></textarea>
        <span class="form-error" id="err-nachricht"></span>
      </div>
      <?php if (!empty($hckey)): ?>
      <div class="h-captcha" data-sitekey="<?php echo htmlspecialchars($hckey); ?>"></div>
      <?php endif; ?>
      <div class="form-msg" id="form-msg"></div>
      <button type="submit" class="btn-submit" id="btn-submit">Nachricht senden</button>
    </form>
  </div>
</div>

<?php elseif ($page === 'impressum'): ?>

<div class="wrap">
  <div class="legal">
    <div class="sec-label">Rechtliches</div>
    <h1>Impressum</h1>
    <h2>Verantwortlich für diese Website</h2>
    <p>
      <?php echo htmlspecialchars($firma['name']); ?><br>
      <?php echo htmlspecialchars($firma['inhaber']); ?><br>
      <?php if (!empty($firma['strasse'])): ?>
        <?php echo htmlspecialchars($firma['strasse']); ?><br>
        <?php echo htmlspecialchars($firma['plz'] . ' ' . $firma['ort']); ?><br>
      <?php endif; ?>
      <?php echo htmlspecialchars($firma['kanton'] . ', ' . $firma['land']); ?><br>
      UID: <?php echo htmlspecialchars($firma['uid']); ?><br>
      <a href="mailto:<?php echo htmlspecialchars($firma['mail']); ?>"><?php echo htmlspecialchars($firma['mail']); ?></a>
    </p>
    <h2>Haftungsausschluss</h2>
    <p>Die Inhalte dieser Website wurden mit Sorgfalt erstellt. Für die Richtigkeit, Vollständigkeit und Aktualität kann jedoch keine Gewähr übernommen werden.</p>
    <p>Für externe Links zu fremden Websites wird keine Verantwortung übernommen. Für den Inhalt verlinkter Seiten sind ausschliesslich deren Betreiber verantwortlich.</p>
    <h2>Gerichtsstand</h2>
    <p>Es gilt Schweizer Recht. Gerichtsstand ist <?php echo htmlspecialchars($firma['kanton']); ?>, <?php echo htmlspecialchars($firma['land']); ?>.</p>
    <p style="margin-top:2rem;"><a href="/?page=datenschutz">Zur Datenschutzerklärung</a></p>
  </div>
</div>

<?php elseif ($page === 'datenschutz'): ?>

<div class="wrap">
  <div class="legal">
    <div class="sec-label">Rechtliches</div>
    <h1>Datenschutzerklärung</h1>
    <p>Stand: September 2026. Diese Erklärung gilt für wittwer-informatik.ch und richtet sich nach dem Schweizer Datenschutzgesetz (nDSG, in Kraft seit 1. September 2023).</p>
    <h2>Verantwortlicher</h2>
    <p>
      <?php echo htmlspecialchars($firma['name'] . ', ' . $firma['inhaber']); ?><br>
      <?php echo htmlspecialchars($firma['kanton'] . ', ' . $firma['land']); ?><br>
      UID: <?php echo htmlspecialchars($firma['uid']); ?><br>
      <a href="mailto:<?php echo htmlspecialchars($firma['mail']); ?>"><?php echo htmlspecialchars($firma['mail']); ?></a>
    </p>
    <h2>Welche Daten erhoben werden</h2>
    <p>Beim Besuch dieser Website speichert der Webserver automatisch technische Zugriffsdaten: IP-Adresse, Zeitpunkt des Zugriffs, aufgerufene Seite, verwendeter Browser und Betriebssystem. Diese Daten sind für den technischen Betrieb notwendig und werden nach spätestens 30 Tagen gelöscht.</p>
    <p>Wenn du über das Kontaktformular oder per E-Mail Kontakt aufnimmst, werden Name, E-Mail-Adresse und Nachrichteninhalt ausschliesslich zur Bearbeitung deiner Anfrage verwendet. Es findet keine Weitergabe an Dritte statt.</p>
    <h2>Zweck der Datenbearbeitung</h2>
    <p>Technische Zugriffsdaten: Sicherstellung des Betriebs und Sicherheit der Website. Kontaktanfragen: Bearbeitung und Beantwortung deiner Anfrage.</p>
    <h2>Drittdienste</h2>
    <p>Diese Website lädt Schriftarten (Ubuntu, Inter) über Google Fonts von Servern von Google LLC in den USA. Dabei wird deine IP-Adresse an Google übermittelt. Für die USA gilt seit dem Swiss-U.S. Data Privacy Framework (gültig ab 15. September 2024) ein angemessenes Datenschutzniveau für zertifizierte Unternehmen. Google LLC ist unter diesem Framework zertifiziert.</p>
    <p>Das Kontaktformular ist mit hCaptcha geschützt (Intuition Machines, Inc., USA). hCaptcha erhebt technische Daten zur Spam-Erkennung. Weitere Informationen unter <a href="https://www.hcaptcha.com/privacy" target="_blank" rel="noopener">hcaptcha.com/privacy</a>.</p>
    <p>Es werden keine weiteren Drittdienste eingesetzt. Kein Google Analytics, keine Social-Media-Einbindungen, keine Werbenetzwerke.</p>
    <h2>Cookies</h2>
    <p>Diese Website setzt keine Tracking-Cookies. Es werden ausschliesslich technisch notwendige Session-Cookies des Webservers sowie Cookies von hCaptcha zur Spam-Erkennung verwendet. Ein Cookie-Banner ist nach Schweizer Recht nicht erforderlich.</p>
    <h2>Auslandtransfers</h2>
    <p>Auslandtransfers: Google Fonts (USA) und hCaptcha (USA), wie oben beschrieben. Alle anderen Daten verbleiben auf Servern in der Schweiz oder der EU (OVHcloud, Frankreich).</p>
    <h2>Deine Rechte nach nDSG</h2>
    <p>Du hast folgende Rechte bezüglich deiner Personendaten:</p>
    <ul>
      <li>Auskunft über gespeicherte Daten</li>
      <li>Berichtigung unrichtiger Daten</li>
      <li>Löschung oder Einschränkung der Bearbeitung</li>
      <li>Datenherausgabe (Datenportabilität)</li>
    </ul>
    <p>Für entsprechende Anfragen: <a href="mailto:<?php echo htmlspecialchars($firma['mail']); ?>"><?php echo htmlspecialchars($firma['mail']); ?></a></p>
    <h2>Aufsichtsbehörde</h2>
    <p>Zuständige Aufsichtsbehörde ist der Eidgenössische Datenschutz- und Öffentlichkeitsbeauftragte (EDÖB). Weitere Informationen unter <a href="https://www.edoeb.admin.ch" target="_blank" rel="noopener">edoeb.admin.ch</a>.</p>
    <h2>Änderungen</h2>
    <p>Diese Datenschutzerklärung kann jederzeit angepasst werden. Die jeweils aktuelle Version ist auf dieser Seite abrufbar.</p>
  </div>
</div>

<?php endif; ?>

<div class="wrap">
  <div class="footer-inner">
    <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($firma['name'] . ', ' . $firma['inhaber']); ?></p>
    <div class="footer-links">
      <a href="/?page=impressum">Impressum</a>
      <a href="/?page=datenschutz">Datenschutz</a>
    </div>
  </div>
</div>

<script>
// Modal-Daten aus PHP
const PROJEKTE = <?php echo json_encode($modal_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

function openModal(slug) {
  const p = PROJEKTE[slug];
  if (!p) return;

  // Bilder
  const hauptbild = document.getElementById('modal-img-main');
  hauptbild.src = p.bilder[0] || '';
  hauptbild.alt = p.name;

  // Thumbnails
  const thumbs = document.getElementById('modal-thumbs');
  thumbs.innerHTML = '';
  if (p.bilder.length > 1) {
    p.bilder.forEach((src, i) => {
      const img = document.createElement('img');
      img.src = src; img.alt = p.name + ' Bild ' + (i + 1);
      img.className = 'modal-thumb' + (i === 0 ? ' active' : '');
      img.onclick = () => {
        hauptbild.src = src;
        thumbs.querySelectorAll('.modal-thumb').forEach(t => t.classList.remove('active'));
        img.classList.add('active');
      };
      thumbs.appendChild(img);
    });
  }

  document.getElementById('modal-title').textContent = p.name;
  document.getElementById('modal-beschreibung').textContent = p.beschreibung;

  const ul = document.getElementById('modal-features');
  ul.innerHTML = '';
  p.features.forEach(f => {
    const li = document.createElement('li');
    li.textContent = f;
    ul.appendChild(li);
  });

  const stack = document.getElementById('modal-stack');
  stack.innerHTML = '';
  p.stack.forEach(s => {
    const span = document.createElement('span');
    span.textContent = s;
    stack.appendChild(span);
  });

  const link = document.getElementById('modal-link');
  if (p.url) {
    link.href = p.url;
    link.textContent = p.url_label;
    link.style.display = 'inline-block';
  } else {
    link.style.display = 'none';
  }

  document.getElementById('modal-overlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modal-overlay').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('modal-overlay').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeModal();
});

// Burger-Menü (Mobile, Slide-in von rechts)
const navBurger = document.getElementById('nav-burger');
const navLinks = document.getElementById('nav-links');
const navBackdrop = document.getElementById('nav-backdrop');

function setMenu(open) {
  navBurger.classList.toggle('open', open);
  navLinks.classList.toggle('open', open);
  navBackdrop.classList.toggle('open', open);
  navBurger.setAttribute('aria-expanded', open ? 'true' : 'false');
  navBurger.setAttribute('aria-label', open ? 'Menü schliessen' : 'Menü öffnen');
  document.body.style.overflow = open ? 'hidden' : '';
}

if (navBurger) {
  navBurger.addEventListener('click', () => setMenu(!navLinks.classList.contains('open')));
  navBackdrop.addEventListener('click', () => setMenu(false));
  navLinks.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') setMenu(false); });
  window.addEventListener('resize', () => {
    if (window.innerWidth > 720 && navLinks.classList.contains('open')) setMenu(false);
  });
}

// Kontaktformular
const form = document.getElementById('contact-form');
if (form) {
  form.addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('btn-submit');
    const msg = document.getElementById('form-msg');

    // Fehler zurücksetzen
    ['name','mail','nachricht'].forEach(f => {
      document.getElementById('err-' + f).style.display = 'none';
    });
    msg.style.display = 'none';

    // Validierung
    let ok = true;
    const name = document.getElementById('cf-name').value.trim();
    const mail = document.getElementById('cf-mail').value.trim();
    const nachricht = document.getElementById('cf-nachricht').value.trim();

    if (name.length < 2) {
      document.getElementById('err-name').textContent = 'Bitte gib deinen Namen ein.';
      document.getElementById('err-name').style.display = 'block';
      ok = false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail)) {
      document.getElementById('err-mail').textContent = 'Bitte gib eine gültige E-Mail-Adresse ein.';
      document.getElementById('err-mail').style.display = 'block';
      ok = false;
    }
    if (nachricht.length < 10) {
      document.getElementById('err-nachricht').textContent = 'Bitte schreib etwas (mindestens 10 Zeichen).';
      document.getElementById('err-nachricht').style.display = 'block';
      ok = false;
    }
    if (!ok) return;

    btn.disabled = true;
    btn.textContent = 'Wird gesendet...';

    const data = new FormData(form);
    try {
      const res = await fetch('/contact.php', { method: 'POST', body: data });
      const json = await res.json();
      if (json.ok) {
        msg.textContent = 'Danke für deine Nachricht. Ich melde mich bald.';
        msg.className = 'form-msg ok';
        msg.style.display = 'block';
        form.reset();
      } else {
        const fehler = json.fehler ? json.fehler.join(' ') : 'Ein Fehler ist aufgetreten.';
        msg.textContent = fehler;
        msg.className = 'form-msg err';
        msg.style.display = 'block';
      }
    } catch {
      msg.textContent = 'Verbindungsfehler. Bitte direkt an hallo@wittwer-informatik.ch schreiben.';
      msg.className = 'form-msg err';
      msg.style.display = 'block';
    }

    btn.disabled = false;
    btn.textContent = 'Nachricht senden';
  });
}
</script>

</body>
</html>
