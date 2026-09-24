# CLAUDE.md: Wittwer Informatik Website

Dieses Dokument ist die Arbeitsanweisung für Claude Code.
Vor jeder Änderung lesen. Nicht überspringen.

---

## Projekt

Statische Marketing-Website für Wittwer Informatik.
Einfacher PHP 8 Webserver, keine Frameworks, keine Dependencies.
Ziel: schlank, wartbar, kein Overhead.

## Dateistruktur

```
index.php           Hauptseite (alle Seiten: home, impressum, datenschutz), CSS inline
config.php          Firmendaten (inkl. Kontakt-Mail), SEO-Werte. Steht in .gitignore.
config.example.php  Vorlage für config.php mit gleicher Struktur (im Repo)
projects.php        Projektliste als PHP-Array. Hier Projekte hinzufügen/bearbeiten.
.htaccess           HTTPS-Redirect, kein Directory-Listing
robots.txt          Crawler-Regeln
sitemap.xml         Sitemap für Google Search Console
llms.txt            Kurzprofil für KI-Indexierung
llms-full.txt       Vollprofil für KI-Indexierung
.github/workflows/  CI: Syntax-Check, Smoke-Test, Security-Check
assets/
  main.js           Modal- und Burger-Menü-Logik
  fonts/            Ubuntu und Inter als woff2 (lokal)
  images/logos/     Logo (logo_transparent_250x64.png wird verwendet)
  projects/
    {slug}/         Projektbilder (hero.jpg, weitere JPGs). 1200x750px, JPG 80%.
```

## Regeln

### Allgemein
- Kein Composer, keine npm, keine externen PHP-Libraries.
- Kein jQuery, kein Alpine.js, kein Framework. Nur Vanilla JS.
- Google Fonts lokal unter assets/fonts/ (ubuntu-400.woff2, ubuntu-700.woff2, inter-400.woff2, inter-500.woff2). Nicht über CDN laden.
- Keine externen Dienste (Scripts, Fonts, iFrames). Die CSP in index.php erlaubt nur `'self'`.
- `script-src` ist ohne `'unsafe-inline'`. Kein Inline-JavaScript und keine `onclick`/`onkeydown`-Attribute in index.php. Daten für JS als `<script type="application/json">`-Block übergeben.
- Alle Texte in Schweizer Schreibstil: kein Gedankenstrich (weder Geviertstrich noch Halbgeviertstrich). Punkt und Komma. Ganz selten ein Semikolon.

### PHP
- PHP 8.x, kein Strict-Mode nötig für diese Seite.
- `config.php` und `projects.php` geben ein Array zurück (`return [...];`).
- Alle Ausgaben mit `htmlspecialchars()` escapen. In `<script>`-Blöcken (JSON-LD, Projektdaten) stattdessen `json_encode()` mit `$json_flags` (enthält `JSON_HEX_TAG`).

### CSS
- CSS-Variablen für Farben und Abstände (bereits definiert in index.php).
- Primärfarbe: `--green: #3CB975`
- Keine Inline-Styles. Zustände aus JS über Klassen setzen (z.B. `body.no-scroll`).
- Mobile-first: unter 720px greift der Media-Query.

### JavaScript
- Kein `document.write`, kein `eval`.
- Modal- und Burger-Menü-Logik liegen in `assets/main.js`. index.php bindet die Datei mit `?v=filemtime` ein (Cache-Busting).
- Das Modal existiert nur auf der Startseite. Code dafür muss prüfen, ob die Elemente vorhanden sind.

### Sicherheit
- Kein Kontaktformular. Kontakt läuft über einen `mailto:`-Link mit vorausgefülltem Betreff.
- Mail-Adresse kommt aus `$firma['mail']` in `config.php`, nie hardcoded im HTML.
- `config.php` ist in `.gitignore`, echte Keys kommen nie ins Repo.

## Konfiguration anpassen

Firmendaten und E-Mail: nur in `config.php` ändern.
Projekte hinzufügen oder bearbeiten: nur in `projects.php`.
Beides wird automatisch in index.php, Impressum und Datenschutz übernommen.

## Bilder

Projektbilder unter `assets/projects/{slug}/` ablegen.
Pfade in `projects.php` im Array `bilder` eintragen.
Empfohlene Grösse: 1200x750px, JPG 80%.
Platzhalter werden automatisch angezeigt solange keine Bilder vorhanden sind.

## Branching

Die Seite ist klein und wird solo betrieben. Ein Branch reicht normalerweise.

```
main    produktiv, läuft auf wittwer-informatik.ch
```

Wenn doch mal etwas Grösseres gebaut wird (z.B. Blog, neues Seitenkonzept):

```
main          produktiv
feature/xyz   neue Funktion, wird nach Test in main gemergt
```

Regel: Direkt auf `main` pushen ist okay für kleine Korrekturen (Texte, Farben, Config).
Für strukturelle Änderungen (neue Seite, Modal-Umbau, JS-Logik) kurz einen Feature-Branch nehmen.

## GitHub Actions

Drei Workflows schützen die Seite vor groben Fehlern. Smoke-Test und Security-Check
erstellen sich im CI eine eigene `config.php` mit Testwerten, weil die echte in `.gitignore` steht.

### 1. PHP-Syntax-Check (`php-check.yml`, bei jedem Push und PR)

`php -l` auf `index.php`, `config.example.php` und `projects.php`.
`config.example.php` steht stellvertretend für `config.php`, die Struktur ist gleich.

### 2. Smoke-Test (`smoke-test.yml`, bei Push und PR auf main)

Startet die Seite mit `php -S` und prüft:
alle drei Seiten antworten mit 200, unbekannte Seite landet auf Home,
Kontakt-Link mit Betreff vorhanden, keine PHP-Fehler auf allen Seiten,
`main.js`, `robots.txt` und `sitemap.xml` erreichbar.

### 3. Security-Check (`security-check.yml`, bei Push und PR auf main, jeden Montag 07:00 UTC)

Testet gegen gängige OWASP-Kategorien: Path Traversal und reflektiertes XSS über den
`page`-Parameter, PHP-Fehlerausgabe auf allen Seiten, CSP ohne `'unsafe-inline'` bei
`script-src`, kein phpinfo, direkter Abruf von `config.php` und `projects.php`.
Dazu grep-Checks auf `eval()`, `exec()`, `var_dump()`, hardcodierte Keys,
unescapte `echo $...`-Ausgaben und Inline-Event-Handler.

## Deployment

Kein automatisches Deployment eingerichtet. Manuell via SFTP oder rsync auf den Webserver.

Empfohlener Deployment-Befehl (von lokalem Rechner):

```bash
rsync -avz --exclude='.git' --exclude='.github' --exclude='.gitignore' \n  --exclude='CLAUDE.md' --exclude='config.php' --exclude='config.example.php' \n  ./ user@server:/var/www/wittwer-informatik.ch/
```

`config.php` wird bewusst ausgeschlossen, die Produktiv-Version liegt nur auf dem Server.
Repo-interne Dateien (CLAUDE.md, Workflows, config.example.php) gehören nicht auf den Webserver.

## Was nicht geändert werden soll

- Das Logo (`assets/images/logos/logo_transparent_250x64.png`) nicht ohne Absprache ersetzen.
- `robots.txt` und `sitemap.xml` bei neuen Seiten aktualisieren.
- `llms.txt` und `llms-full.txt` bei inhaltlichen Änderungen mitpflegen.
