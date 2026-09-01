# CLAUDE.md — Wittwer Informatik Website

Dieses Dokument ist die Arbeitsanweisung für Claude Code.
Vor jeder Änderung lesen. Nicht überspringen.

---

## Projekt

Statische Marketing-Website für Wittwer Informatik.
Einfacher PHP 8 Webserver, keine Frameworks, keine Dependencies.
Ziel: schlank, wartbar, kein Overhead.

## Dateistruktur

```
index.php        — Hauptseite (alle Seiten: home, impressum, datenschutz)
config.php       — Firmendaten, hCaptcha-Keys, SEO-Werte. NIE committen mit echten Keys.
projects.php     — Projektliste als PHP-Array. Hier Projekte hinzufügen/bearbeiten.
contact.php      — Formular-Handler (POST, hCaptcha-Verifikation, mail())
robots.txt       — Crawler-Regeln
sitemap.xml      — Sitemap für Google Search Console
llms.txt         — Kurzprofil für KI-Indexierung
llms-full.txt    — Vollprofil für KI-Indexierung
assets/
  projects/
    {slug}/      — Projektbilder (hero.jpg, weitere JPGs). 1200x750px, JPG 80%.
```

## Regeln

### Allgemein
- Kein Composer, keine npm, keine externen PHP-Libraries.
- Kein jQuery, kein Alpine.js, kein Framework. Nur Vanilla JS.
- Google Fonts über CDN (bereits eingebunden): Ubuntu, Inter.
- hCaptcha-Script wird nur auf der Startseite geladen.
- Alle Texte in Schweizer Schreibstil: kein Gedankenstrich (weder — noch –). Punkt und Komma. Ganz selten ein Semikolon.

### PHP
- PHP 8.x, kein Strict-Mode nötig für diese Seite.
- `config.php` und `projects.php` geben ein Array zurück (`return [...];`).
- Alle Ausgaben mit `htmlspecialchars()` escapen.
- `contact.php` gibt immer JSON zurück.

### CSS
- CSS-Variablen für Farben und Abstände (bereits definiert in index.php).
- Primärfarbe: `--green: #3CB975`
- Keine Inline-Styles ausser für dynamische Werte.
- Mobile-first: unter 720px greift der Media-Query.

### JavaScript
- Kein `document.write`, kein `eval`.
- Modal-Logik und Formular-Handler sind in index.php am Ende im `<script>`-Block.
- Burger-Menü-Logik ebenfalls dort.

### Sicherheit
- `contact.php`: Eingaben immer mit `strip_tags()` und `trim()` bereinigen.
- hCaptcha-Secret-Key nur in `config.php`, nie hardcoded.
- `config.php` ist in `.gitignore` — echte Keys kommen nie ins Repo.

## Konfiguration anpassen

Firmendaten, E-Mail, hCaptcha-Keys: nur in `config.php` ändern.
Projekte hinzufügen oder bearbeiten: nur in `projects.php`.
Beides wird automatisch in index.php, Impressum und Datenschutz übernommen.

## Bilder

Projektbilder unter `assets/projects/{slug}/` ablegen.
Dateinamen in `projects.php` im Array `bilder` eintragen (auskommentierte Zeilen aktivieren).
Empfohlene Grösse: 1200x750px, JPG 80%.
Platzhalter werden automatisch angezeigt solange keine Bilder vorhanden sind.

## Branching

Die Seite ist klein und wird solo betrieben. Ein Branch reicht normalerweise.

```
main    — produktiv, läuft auf wittwer-informatik.ch
```

Wenn doch mal etwas Grösseres gebaut wird (z.B. Blog, neues Seitenkonzept):

```
main          — produktiv
feature/xyz   — neue Funktion, wird nach Test in main gemergt
```

Regel: Direkt auf `main` pushen ist okay für kleine Korrekturen (Texte, Farben, Config).
Für strukturelle Änderungen (neue Seite, Modal-Umbau, JS-Logik) kurz einen Feature-Branch nehmen.

## GitHub Actions

Zwei einfache Workflows schützen die Seite vor groben Fehlern.

### 1. PHP-Syntax-Check (bei jedem Push)

Datei: `.github/workflows/php-check.yml`

```yaml
name: PHP Syntax Check

on: [push, pull_request]

jobs:
  syntax:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: PHP 8 installieren
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Syntax prüfen
        run: |
          php -l index.php
          php -l config.example.php
          php -l projects.php
          php -l contact.php
```

Hinweis: `config.php` steht in `.gitignore` und ist im CI-Checkout nicht vorhanden.
Der Syntax-Check prüft darum `config.example.php`. Die beiden Dateien haben die
gleiche Struktur, ein Syntaxfehler in einer fällt so trotzdem auf.

### 2. Link- und Response-Check (bei Push auf main)

Datei: `.github/workflows/smoke-test.yml`

```yaml
name: Smoke Test

on:
  push:
    branches: [main]

jobs:
  smoke:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: PHP 8 installieren
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      - name: Lokalen Server starten
        run: php -S localhost:8080 &
      - name: Warten bis Server bereit
        run: sleep 2
      - name: Startseite erreichbar
        run: curl -sf http://localhost:8080/ > /dev/null
      - name: Impressum erreichbar
        run: curl -sf "http://localhost:8080/?page=impressum" > /dev/null
      - name: Datenschutz erreichbar
        run: curl -sf "http://localhost:8080/?page=datenschutz" > /dev/null
      - name: Kontaktformular antwortet
        run: |
          STATUS=$(curl -s -o /dev/null -w "%{http_code}" -X POST http://localhost:8080/contact.php)
          [ "$STATUS" = "405" ] && echo "OK: contact.php blockiert GET/leere POST" || exit 1
```

Der Smoke-Test startet die Seite lokal und prüft, ob alle drei Seiten antworten und `contact.php` keine leeren Anfragen durchlässt. Er braucht keine echten hCaptcha-Keys weil er keinen Submit auslöst.

## Deployment

Kein automatisches Deployment eingerichtet. Manuell via SFTP oder rsync auf den Webserver.

Empfohlener Deployment-Befehl (von lokalem Rechner):

```bash
rsync -avz --exclude='.git' --exclude='config.php' ./ user@server:/var/www/wittwer-informatik.ch/
```

`config.php` wird bewusst ausgeschlossen — die Produktiv-Version liegt nur auf dem Server.

## Was nicht geändert werden soll

- Das Logo (`logo_250x64.png` als Base64 in index.php) nicht ohne Absprache ersetzen.
- Die hCaptcha-Integration in `contact.php` nicht vereinfachen oder entfernen.
- `robots.txt` und `sitemap.xml` bei neuen Seiten aktualisieren.
- `llms.txt` und `llms-full.txt` bei inhaltlichen Änderungen mitpflegen.
