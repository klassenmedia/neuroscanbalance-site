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

- **Preise/Termine ändern = IMMER alle Fundstellen synchron halten:**
  1. Sichtbarer Text in `index.html` (Intensiv-Bereich, FAQ, Kontakt-Box)
  2. JSON-LD-Schema im `<head>` (makesOffer, FAQPage, priceRange)
  3. `llms.txt` (Preise + Terminliste)
  4. `kosten.html` (seit SEO-Briefing Paket 2/4: nennt 80 € + „Preis auf Anfrage" fürs Intensive)
  5. `blog/ablauf-einzel-lesson-und-intensiv-wochenende.html` (nennt ebenfalls 80 €)
  6. `blog/stiftung-kostenuebernahme-foerderangebot.html` (nennt 80 €; bis zur
     Veröffentlichung unter `blog/drafts/`)
  Die llms.txt ist schon zweimal auseinandergelaufen – nie wieder. Bei neuen
  Seiten/Artikeln mit Preisnennung: hier in der Liste ergänzen.
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
- **Schriften werden selbst gehostet** (`assets/fonts/`, `@font-face` ganz oben
  in `style.v3.css`) – NIE wieder über `fonts.googleapis.com` einbinden.
  Beim Laden von der Google-CDN geht die IP jedes Besuchers ohne Einwilligung
  an Google in die USA (vgl. LG München I, 20.01.2022, Az. 3 O 17493/20;
  seitdem Abmahnwelle). Inter und Caveat stehen unter der SIL Open Font
  License, Self-Hosting ist erlaubt. Neue HTML-Seiten brauchen daher **keine**
  preconnects und keinen Font-Link mehr, nur `style.v3.css`. Wird ein neuer
  Schriftschnitt gebraucht, die `.woff2` nach `assets/fonts/` legen und einen
  `@font-face`-Block ergänzen – `unicode-range` beibehalten, damit der Browser
  nur lädt, was er braucht.
- **SEO-Head-Standard** (schon gesetzt, so lassen): `robots` mit
  `max-image-preview:large`, `theme-color`, `geo.*`-Meta, vollständige
  Open-Graph- + Twitter-Card-Tags, JSON-LD mit LocalBusiness + WebSite +
  Person (inkl. `hasCredential`) + FAQPage.

## Content-Standards (SEO/GEO, seit Briefing 28.07.)

- **Antwort-Kapsel-Regel:** Der erste Absatz nach jeder `<h2>` in Blogartikeln
  und Unterseiten sollte 45–60 Wörter umfassen, die Überschrift im ersten Satz
  direkt beantworten, ohne Links auskommen und auch isoliert (z. B. als
  KI-Snippet zitiert) verständlich sein. Gilt für NEUE Abschnitte; bestehende
  Artikel wurden dafür nicht rückwirkend umgeschrieben.
- **Interne Verlinkung:** Blogartikel sollten 3–5 kontextuelle Links im
  Fließtext haben (nicht nur im Weiterlesen-Block), Ankertext beschreibend,
  nicht „hier klicken". Der Weiterlesen-Block verlinkt auf 2–3 passende
  Artikel plus mindestens eine thematische Unterseite (`kinder.html`,
  `erwachsene.html`, `ablauf.html` usw.).
- **Quellen-Block:** Jeder Blogartikel mit medizinischem Bezug bekommt am
  Ende einen Abschnitt „Quellen & weiterführende Informationen" mit 2–3
  externen Links. Nur Startseiten stabiler Institutionen verlinken (z. B.
  `awmf.org`, `bvkj.de`, `kindergesundheit-info.de`,
  `gesundheitsinformation.de`) – keine tiefen Links auf einzelne PDFs/Artikel,
  da die Aktualität von hier aus nicht laufend geprüft werden kann.
- **FAQ je Artikel:** 3–5 Fragen als sichtbarer `<details>`-Block
  (`.faq-plus`/`.faq-answer`-Klassen wiederverwenden) plus passendes
  `FAQPage`-JSON-LD im selben `@graph`. Nur Fragen aufnehmen, die auch
  sichtbar auf der Seite stehen (kein Schema ohne sichtbare Entsprechung).
- **Vergleichstabellen:** in `.article-table-wrap` (CSS in `style.v3.css`)
  wickeln – erzeugt `overflow-x:auto`, damit Tabellen auf 390/360px nicht die
  Seite sprengen.

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
404.html             – eigene Fehlerseite (ErrorDocument 404 in .htaccess)
ueber-mich.html / ablauf.html / intensiv-wochenende.html
kinder.html / erwachsene.html / kosten.html   – Themen-Unterseiten (SEO Paket 2)
neuroscanbalance-osnabrueck.html / neuroscanbalance-minden.html – lokale Landingpages
llms.txt            – KI-Steckbrief (synchron halten!)
robots.txt          – erlaubt KI-Crawler explizit (GPTBot, ClaudeBot, …)
sitemap.xml
.htaccess           – Canonical-Redirects (https, ohne www), 301 fuer Alt-URLs, Kompression aus
assets/css/style.v3.css   – EINZIGE aktive CSS-Datei (style.css = Altstand)
assets/css/animations.css
assets/js/main.js         – Menü, Slider, Calendly, Cookie-Banner
assets/js/animations.js   – Reveals, Parallax, Count-up
.github/workflows/deploy.yml – FTP-Deploy + Auto-Tagging
```
