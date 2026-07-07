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

- **Preise/Termine ändern = IMMER 3 Orte synchron halten:**
  1. Sichtbarer Text in `index.html` (Intensiv-Bereich, FAQ, Kontakt-Box)
  2. JSON-LD-Schema im `<head>` (makesOffer, FAQPage, priceRange)
  3. `llms.txt` (Preise + Terminliste)
  Die llms.txt ist schon zweimal auseinandergelaufen – nie wieder.
- **Kein `aggregateRating` im Schema**, solange der Testimonial-Bereich
  auskommentiert ist (Google-Richtlinie: Markup ohne sichtbare Entsprechung
  riskiert Rich-Snippet-Abschaltung). Erst wieder rein, wenn echte
  freigegebene Erfahrungsberichte sichtbar auf der Seite stehen.
- **Der Intensiv-Wochenende-Preis steht bewusst NICHT auf der Seite**
  („Preis auf Anfrage"). Nicht wieder einfügen.
- Nach Content-Änderungen: `lastmod` der Startseite in `sitemap.xml` aktualisieren.
- Wording auf der Seite: „Intensive" (nicht „Intensivblock/Intensiv-Wochenende"
  im Fließtext – so hat es der Kunde in seiner Version etabliert).

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
assets/js/main.js         – Menü, Slider, Calendly, Cookie-Banner
assets/js/animations.js   – Reveals, Parallax, Count-up
.github/workflows/deploy.yml – FTP-Deploy + Auto-Tagging
```
