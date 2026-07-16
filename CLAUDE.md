# NeuroScanBalance Bad Essen – Projektregeln

Statische Kunden-Website (HTML/CSS/JS, kein Framework, kein Build-Schritt).
Live: https://neuroscanbalance-badessen.de · Hosting: All-Inkl (FTP) · Deploy: GitHub Actions.

## Ablauf einer Änderung

1. Änderung auf `main` committen und pushen.
2. GitHub Action „Deploy zu All-Inkl (FTP)" lädt automatisch per FTPS hoch (~1 Min).
3. Nach Erfolg wird der Commit automatisch getaggt: `live-JJJJ-MM-TT-HHMMSS`.
   → Reiter „Tags" zeigt, welche Version wann live ging.

**Rollback:** `git revert <commit>` auf main pushen (sauberster Weg), oder gezielt:
`git checkout live-<zeitstempel> -- <datei>` und neu committen.

## Harte Regeln (Konsistenz)

- **Intensiv-Termine + Intensiv-Preis werden seit Juli 2026 NICHT mehr in
  `index.html` gepflegt, sondern von Willi selbst im Admin-Bereich
  (`/admin/`, Login) über `termine.json`** (Domain-Root, NICHT in Git –
  siehe „Admin-Bereich" unten). `index.html` und `anmeldung.html` laden die
  Termine per `fetch('termine.json')` und ersetzen damit die im HTML fest
  eingebaute Liste. Diese feste Liste bleibt als **Fallback** im HTML stehen
  (falls `termine.json` mal fehlt/nicht lädt) – beim Ändern eines Termins
  im Admin-Bereich muss man diesen HTML-Fallback NICHT anfassen; er ist nur
  ein Sicherheitsnetz, keine Quelle der Wahrheit mehr.
- **`llms.txt` und das JSON-LD-Schema (`makesOffer`, `priceRange`) bleiben
  weiterhin manuell zu pflegen** und laufen NICHT automatisch mit
  `termine.json` mit (kein serverseitiges Nachziehen der Termine-Liste in
  diese beiden Stellen – bewusste Einschränkung, um SEO-relevantes
  statisches Markup nicht von Client-JS abhängig zu machen). Bei größeren
  Terminänderungen weiterhin von Hand synchron halten.
- **Kein `aggregateRating` im Schema**, solange der Testimonial-Bereich
  auskommentiert ist (Google-Richtlinie: Markup ohne sichtbare Entsprechung
  riskiert Rich-Snippet-Abschaltung). Erst wieder rein, wenn echte
  freigegebene Erfahrungsberichte sichtbar auf der Seite stehen.
- **Startseite zeigt weiterhin „Preis auf Anfrage"** für das Intensiv-
  Wochenende (unverändert). **Im Buchungsformular (`anmeldung.html`) und in
  der Eltern-Bestätigungsmail wird der Preis dagegen angezeigt**, sobald
  Willi ihn im Admin-Bereich unter „Preise" hinterlegt (Feld leer lassen =
  weiterhin „Preis auf Anfrage" auch im Formular). Das ist eine bewusste
  Ausnahme von der alten Regel, auf Wunsch des Kunden eingeführt, damit
  Familien beim Buchen wissen, was sie überweisen sollen.
- Nach Content-Änderungen: `lastmod` der Startseite in `sitemap.xml` aktualisieren.
- Wording auf der Seite: „Intensive" (nicht „Intensivblock/Intensiv-Wochenende"
  im Fließtext – so hat es der Kunde in seiner Version etabliert).

## Admin-Bereich (`/admin/`)

Login-geschützter Bereich, in dem Willi selbst Intensiv-Termine (Ort,
Start-/Enddatum), Preise (Einzel-Lesson, Intensive) und Zahlungsdaten
(IBAN etc. für die Bestätigungsmail) pflegt – ohne Code, ohne Git.

- **Datenablage bewusst außerhalb von Git** (siehe `.gitignore` +
  Deploy-`exclude` in beiden Workflows): `termine.json` (Domain-Root,
  öffentlich per `fetch()` lesbar – nur Orte/Daten/Preise, keine Bankdaten),
  `zahlung-config.php` (Domain-Root, NIE öffentlich abrufbar,
  `.htaccess`-geschützt, nur serverseitig von `anmeldung.php` gelesen),
  `admin/admin-config.php` (Login-Zugangsdaten, bcrypt-Hash).
  **Warum außerhalb von Git:** Der automatische Deploy lädt bei jedem Push
  den Repo-Stand per FTP hoch. Läge Willis Datenpflege in einer
  Git-verfolgten Datei, würde der nächste Deploy seine Änderungen wieder
  überschreiben. So kommen sich Code-Updates und Admin-Pflege nie in die Quere.
- **Vor Suchmaschinen versteckt:** `robots.txt` (`Disallow: /admin/`),
  `admin/.htaccess` setzt `X-Robots-Tag: noindex` + `Cache-Control: no-store`.
- **Login:** PHP-Session + bcrypt (kein Apache-`.htpasswd`, portabler).
  CSRF-Token auf jedem Formular. Passwort kann Willi selbst im Admin-Bereich
  ändern (Abschnitt „Passwort ändern").
- **Ersteinrichtung auf einem neuen/frischen Server:**
  `admin/admin-config.example.php` → `admin/admin-config.php` kopieren
  (Zugangsdaten eintragen), `smtp-config.example.php`-Analogon für Termine
  gibt es nicht – `termine.json` erzeugt sich beim ersten Speichern im
  Admin-Bereich automatisch; für den produktiven Start wurde einmalig eine
  vorbefüllte `termine.json` mit der bestehenden Terminliste per WebFTP
  hochgeladen (sonst wäre die Terminhistorie beim ersten Admin-Save leer).

## Technische Stolperfallen (alle schon passiert)

- **FTP-Zielordner:** Der FTP-Login landet im Account-Root (`w021a97b/`).
  Die Domain liegt eine Ebene tiefer → `server-dir: /neuroscanbalance-badessen.de/`
  in `deploy.yml`. Niemals auf `./` zurückstellen.
- **Kein `backdrop-filter` direkt auf `<nav>`** – erzeugt einen Containing
  Block und quetscht das mobile Vollbild-Menü (`.nav-links.open`,
  `position:fixed`) auf Nav-Höhe zusammen. Der Blur liegt deshalb auf
  `nav::before`. So lassen.
- **Keine gzip/brotli-Kompression aktivieren** – All-Inkl liefert dabei
  kaputte Streams (weiße Seite). Die `.htaccess` schaltet Kompression
  bewusst komplett ab.
- **Stats-Bar & Grids mobil:** `minmax(0,1fr)` statt `1fr` verwenden,
  sonst Überlauf auf schmalen Handys. Responsive-Checks bei 390px UND 360px.
- **Hero-Slider:** Slide 1 lädt sofort (LCP), Slides 2–10 nur per `data-bg`
  (Lazy-Load in `main.js`). Neue Slides ebenfalls mit `data-bg` einbinden.
- **Bilder:** immer `width`/`height` setzen; unterhalb des ersten Viewports
  zusätzlich `loading="lazy"`.
- **`Bilder/` (Rohfotos, ~130 MB)** existiert nur lokal beim Kunden –
  per `.gitignore` und Deploy-exclude ausgeschlossen. Nie einchecken.
- Web-Bilder liegen unter `assets/img/`, Hero-Slides unter `assets/img/hero/`.
- **Google Fonts werden per `<link rel="preconnect">` + `<link rel="stylesheet">`
  im HTML-`<head>` geladen** (index/impressum/datenschutz), NICHT per `@import`
  in der CSS – schneller, kein render-blockierender Wasserfall. Nicht zurück
  auf `@import` stellen. Bei neuen HTML-Seiten dieselben zwei preconnects +
  den Font-Link mitnehmen.
- **SEO-Head-Standard** (schon gesetzt, so lassen): `robots` mit
  `max-image-preview:large`, `theme-color`, `geo.*`-Meta, vollständige
  Open-Graph- + Twitter-Card-Tags, JSON-LD mit LocalBusiness + WebSite +
  Person (inkl. `hasCredential`) + FAQPage.

## Verifizieren vor dem Push

Responsive-Test headless (Chromium liegt unter
`/opt/pw-browsers/chromium-1194/chrome-linux/chrome`, Playwright via
`executablePath` starten): Seite bei 390px und 360px rendern,
`document.documentElement.scrollWidth` darf den Viewport nicht überschreiten;
Hamburger-Menü öffnen und prüfen, dass es vollflächig deckt.
JSON-LD nach Schema-Änderungen mit `json.loads()` gegenprüfen.

## Struktur

```
index.html          – One-Pager (alle Sektionen)
impressum.html / datenschutz.html
llms.txt            – KI-Steckbrief (synchron halten!)
robots.txt          – erlaubt KI-Crawler explizit (GPTBot, ClaudeBot, …)
sitemap.xml
.htaccess           – Canonical-Redirects (https, ohne www), Kompression aus
assets/css/style.v3.css   – EINZIGE aktive CSS-Datei (style.css = Altstand)
assets/css/animations.css
assets/js/main.js         – Menü, Slider, Calendly, Cookie-Banner, Intensiv-Termine (dynamisch)
assets/js/animations.js   – Reveals, Parallax, Count-up
anmeldung.html / anmeldung.php / danke.html – Intensiv-Anmeldeformular
smtp-mailer.php           – abhängigkeitsfreier SMTP-Client (TLS)
admin/index.php           – Login-Bereich für Willi (Termine/Preise/Zahlungsdaten)
.github/workflows/deploy.yml – FTP-Deploy + Auto-Tagging
.github/workflows/schedule-blog.yml – täglicher Cron, veröffentlicht geplante Blogbeiträge

Nicht in Git (nur auf dem Server, siehe .gitignore + Deploy-exclude):
  termine.json, zahlung-config.php, admin/admin-config.php,
  smtp-config.php, formular-log.txt
```
