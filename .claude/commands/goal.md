# /goal — SEO-Briefing umsetzen (71 → 92 Punkte)

Setze das Umsetzungs-Briefing vom 28.07.2026 für neuroscanbalance-badessen.de um.
Arbeite die Pakete **in dieser Reihenfolge** ab. Falls Argumente übergeben wurden
(z. B. `/goal P2 P3`), nur die genannten Pakete ausführen: $ARGUMENTS

## Verbindliche Rahmenregeln (vor dem Start lesen)

1. **CLAUDE.md gilt.** Insbesondere: kein `aggregateRating` ohne sichtbare echte
   Testimonials; Intensiv-Preis nicht auf die Startseite; „Intensive" als Wording;
   nach Content-Änderungen `lastmod` in sitemap.xml; llms.txt synchron halten.
2. **Alle neuen sichtbaren Texte durch den entkiisierer-Skill** (.claude/skills/):
   keine Fettungen im Fließtext, keine Frage-Überschriften-Serien, Dreierketten
   und Symmetrie brechen, Satzlängen 3–40 Wörter mischen. Keine erfundenen
   Anekdoten, Zahlen oder Zitate — fehlende Konkretheit als Lücke für Willi
   ausweisen statt sie zu erfinden.
3. **Design-System wiederverwenden:** styles nur in `assets/css/style.v3.css`
   ergänzen (nie style.css), Fonts per preconnect+link im Head (nie @import),
   Grids mit `minmax(0,1fr)`, Bilder immer mit width/height, unterhalb des
   Viewports `loading="lazy"`. Buttons = bestehende 3D-Klassen (btn-nav,
   article-cta-btn).
4. **Verifizieren vor jedem Push:** Playwright headless
   (`/opt/pw-browsers/chromium-1194/chrome-linux/chrome` via executable_path),
   390px UND 360px, `document.documentElement.scrollWidth` ≤ Viewport. Jede
   JSON-LD-Änderung mit `json.loads()` prüfen. PHP-Dateien mit `php -l`.
5. **Deploy:** Commit auf `main` pushen = automatisch live (~1 Min, Auto-Tag
   `live-*`). Kleine, thematisch getrennte Commits je Paket.
6. **Abschlussbericht:** Am Ende alle geänderten SICHTBAREN Texte auflisten
   (vorher/nachher bei H1/H2-Änderungen) + Screenshots der neuen Seiten senden,
   damit Andreas/Willi sie freigeben oder korrigieren können.

## Status: bereits erledigt am 28.07. (nicht wiederholen)

- ✅ 1.1 301-Redirects für WordPress-Alt-URLs in `.htaccess`
- ✅ 1.2 `404.html` erstellt + `ErrorDocument 404` gesetzt
- ✅ 1.3 Impressum: OS-Plattform-Absatz raus, § 18 Abs. 2 MStV statt RStV

---

## Paket 1 — Rest (Titles, H1, Bilder)

**1.4 Titles & Descriptions.**
- Startseiten-Title (53 Zeichen): `NeuroScanBalance Bad Essen & Osnabrück | Willi Klassen`
- Startseiten-Description: `Sanfte neurologische Förderung für Kinder und Erwachsene in Bad Essen. Zertifizierter NeuroScanBalance-Trainer Willi Klassen. Jetzt Termin vereinbaren.`
- Alle 7 Blog-Descriptions + 2 Drafts auf 150–160 Zeichen bringen. Muster:
  Problem in Frageform → konkrete Antwort andeuten → Handlungsimpuls. Zeichen
  je Description per Python zählen und im Bericht ausgeben.

**1.5 H1 der Startseite erweitern** (SICHTBARER TEXT → im Bericht ausweisen):
- H1: `NeuroScanBalance in Bad Essen — die sanfte Alternative, wenn klassische Therapie an ihre Grenzen kommt. Der Weg über das Gehirn.`
  („Therapie" meint hier die klassische Therapie anderer — das ist ok, siehe 5.4.)
- Erste H2 „Die Ärzte sagten, es gehe nicht." → `Meine Geschichte: Die Ärzte sagten, es gehe nicht.`
- Danach Hero-Layout bei 390/360px prüfen (längere H1 darf nichts sprengen).

**1.6 WebP + Alt-Texte.**
- `willi-klassen-portrait.jpg`, `nsb-logo.png`, `nsb-logo-white.png` zusätzlich
  als WebP erzeugen (Pillow ist installiert), Einbindung per `<picture>` mit
  Fallback. Favicons NICHT anfassen.
- OG-Images (og:image) als JPG/PNG lassen — WhatsApp/Facebook können kein WebP
  in allen Fällen; nur die `<img>`-Einbindungen umstellen.
- Alt-Texte differenzieren: jedes Logo-/Bild-Alt beschreibt, was zu sehen ist;
  keine 3 identischen „NeuroScanBalance Feinmotorik".
- Hero: erstes sichtbares Bild `fetchpriority="high"` und KEIN lazy; Rest lazy.
  Achtung: Hero-Slides laufen über CSS-Backgrounds + data-bg-Lazyload in
  main.js — das System NICHT umbauen, nur echte `<img>` optimieren.

## Paket 2 — Eigene Unterseiten (größter Hebel)

**2.1 Sechs neue Seiten.** Inhalt von der Startseite AUSBAUEN (600–900 Wörter,
entkiisiert), nicht kopieren. Die Startseite behält die Kurzfassung und
verlinkt mit beschreibendem Ankertext auf die Detailseite.

| Datei | Title | H1-Thema |
|---|---|---|
| `ueber-mich.html` | Willi Klassen — NeuroScanBalance Trainer Bad Essen | Willis Geschichte (Tochter, Ausbildung, Zertifizierung) |
| `ablauf.html` | Ablauf einer NeuroScanBalance Lesson \| Bad Essen | Lesson, Intensivblock, Integrationspause |
| `intensiv-wochenende.html` | NeuroScanBalance Intensiv-Wochenende für Kinder | 3 Tage/6 Lessons/2 Trainer, Termine-Verweis, Anmeldung-Link |
| `kinder.html` | NeuroScanBalance für Kinder — für wen sie geeignet ist | Zielgruppen, Diagnosen, ab Säuglingsalter |
| `erwachsene.html` | NeuroScanBalance für Erwachsene nach Schlaganfall | Erwachsene, chronische Einschränkungen |
| `kosten.html` | Was kostet NeuroScanBalance? Preise & Kassenleistung | 80 € Lesson, Intensive auf Anfrage, Selbstzahler, Stiftungen |

Jede Seite: eigene Description (150–160), genau eine H1, sichtbare Breadcrumb
(bestehende .breadcrumb-Klasse), Kontakt-CTA am Ende (article-cta-Klassen),
Links auf 2–3 passende Blogartikel, gleicher Head-Standard wie blog-Seiten
(preconnects, favicon, theme-color, canonical, OG, Matomo).
Inhaltsquellen: index.html-Sektionen, blog-Artikel, llms.txt. KEINE neuen
Fakten erfinden; wo Konkretes fehlt (z. B. Schlaganfall-Erfahrung), allgemein
bleiben und die Lücke im Bericht für Willi notieren.

Speziell für `kosten.html`: einen eigenen Abschnitt „Unterstützung durch
Stiftungen" aufnehmen — es gibt die Möglichkeit, dass eine Stiftung die
Kosten übernimmt (Hinweis von Andreas, 28.07.). Allgemein formulieren
(Stiftungen für Familien mit Kindern mit Behinderung/Entwicklungsstörungen
können auf Antrag Kosten übernehmen; Willi unterstützt beim Antrag, wenn
gewünscht → als Angebot nur aufnehmen, falls Willi das bestätigt). KEINE
konkreten Stiftungsnamen erfinden — im Bericht bei Willi anfragen, mit
welchen Stiftungen es schon geklappt hat, damit die Seite konkret werden kann.

**2.2 Zwei lokale Landingpages:** `neuroscanbalance-osnabrueck.html`,
`neuroscanbalance-minden.html`. Kein Textklon: je eigener Inhalt mit Anfahrt
und realer Fahrzeit (Bad Essen–Osnabrück ca. 30 Min über A33/B51; Minden ca.
30 Min über B65 — vor Verwendung mit Kartenlogik plausibilisieren), ÖPNV-Hinweis
(Bahnhof Bohmte), Einzugsgebiet-Bezug aus llms.txt. Versorgungslage nur nennen,
wenn belegbar — sonst weglassen.

**2.3 Breadcrumbs als Schema:** `BreadcrumbList`-JSON-LD auf allen Unterseiten
UND allen Blogartikeln (sichtbare Breadcrumb existiert im Blog schon).

**Nacharbeiten Paket 2:** alle neuen URLs in sitemap.xml (+ lastmod), llms.txt
um Unterseiten-Liste ergänzen, Startseiten-Nav prüfen (One-Pager-Anker behalten;
Unterseiten werden aus den Sektionen heraus verlinkt, nicht alle in die Nav).

## Paket 3 — Interne Verlinkung

**3.1** Pro Blogartikel 3–5 kontextuelle Links im Fließtext mit beschreibendem
Ankertext (nicht „mehr dazu hier"). Bestehende Absätze dafür minimal umformulieren
ist erlaubt — Ton beibehalten (entkiisiert).
**3.2** Den „Weiterlesen"-Block je Artikel kuratieren: 3 thematisch passende
Artikel (nicht die neuesten) + 1 Link auf die passende neue Unterseite aus Paket 2.

## Paket 4 — GEO-Optimierung

**4.1 Quellen:** Je Artikel einen Block „Quellen & weiterführende Informationen"
mit 2–3 externen Links (`rel="noopener"`, OHNE nofollow). NUR Institutionen
verlinken, deren Startseiten-URLs stabil sind (z. B. awmf.org, bvkj.de,
kindergesundheit-info.de) — keine tiefen Links auf ungeprüfte PDFs, da aus der
Sandbox keine Live-Prüfung möglich ist. Im Bericht vermerken: Willi soll
konkrete Leitlinien/Artikel nachliefern, dann tiefer verlinken.
**4.2 Vergleichstabelle** im Physio-Artikel: Zeilen NeuroScanBalance /
Physiotherapie / Ergotherapie; Spalten Ziel · Ablauf · Dauer · Schmerzen ·
Kostenträger · wann sinnvoll. Nur belegbare Aussagen (NSB-Fakten aus llms.txt;
Physio/Ergo allgemeinüblich formulieren). Mobil: Tabelle in Container mit
`overflow-x:auto`.
**4.3 FAQ je Artikel:** 3–5 echte Elternfragen als sichtbarer Block am
Artikelende + `FAQPage`-JSON-LD je Artikel. HWG-konform (keine Heilversprechen),
entkiisiert. Achtung: FAQPage darf je Seite nur die dort sichtbaren Fragen enthalten.
**4.4 Antwort-Kapsel-Regel** in CLAUDE.md dokumentieren: erster Absatz nach
jeder H2 = 45–60 Wörter, beantwortet die Überschrift im ersten Satz, keine
Links, isoliert verständlich.

## Paket 5 — Trust-Signale

**5.1 Testimonials: NUR VORBEREITEN, NICHT LIVE.** Auskommentierten
Testimonial-Bereich + Schema-Vorlage (aggregateRating/review) als HTML-Kommentar
anlegen. Livegang erst, wenn Willi echte, dokumentierte Bewertungen liefert —
CLAUDE.md-Regel. Im Bericht als offene Aufgabe an Willi ausweisen.
**5.2 ProfilePage-Schema** auf ueber-mich.html, verknüpft mit dem bestehenden
Person-Knoten (`@id: .../#willi` — die im Briefing genannte `#person`-ID ist
falsch, echte ID aus index.html verwenden).
**5.3 Service-Schema** für Einzel-Lesson (80 €) und Intensive (OHNE Preis!) im
JSON-LD der Startseite ergänzen; provider-`@id` = echte LocalBusiness-ID aus
index.html (`#business`, nicht `#localbusiness`).
**5.4 HWG-Wording:** `grep -rn "Therapie\|Behandlung\|behandeln"` über alle
HTML + llms.txt. Ersetzen NUR wo es das EIGENE Angebot bezeichnet
(→ Förderung/Training/Lernmethode/Einheit). „Physiotherapie", „ärztliche
Behandlung" u. ä. (fremde Leistungen) bleiben. Titles/Descriptions/JSON-LD
mitprüfen (z. B. FAQ „Wie läuft eine Behandlung ab?"). Jede Ersetzung im
Bericht listen mit Hinweis: vor Livegang anwaltlich gegenlesen lassen —
deshalb diese Textänderungen auf einem Branch `hwg-wording` sammeln und NICHT
auf main mergen, bis Andreas freigibt.

## Paket 6 — nicht ausführbar durch Claude

`docs/sichtbarkeit-todo-willi.md` erstellen mit den 7 Punkten aus dem Briefing
(GBP-Pflege, Bewertungen sammeln, Bing Webmaster Tools, YouTube, Wikidata,
Markenerwähnungen, Verzeichnis-NAP-Konsistenz) — als abhakbare Liste mit je
2–3 Sätzen Anleitung. docs/ ist vom Deploy ausgeschlossen.

## Abnahme (am Ende ausführen und Ergebnis berichten)

1. `.htaccess`-Redirect-Logik lokal nachvollziehen (Regex gegen Beispiel-URLs
   testen); Live-`curl -I`-Stichprobe kann nur Andreas machen — im Bericht als
   manuellen Prüfschritt übergeben.
2. Jede neue Seite: genau eine H1, eigene Description, Breadcrumb, valides
   JSON-LD (json.loads), kein Overflow bei 390/360px.
3. Jeder Blogartikel: ≥3 interne Links im Fließtext, ≥2 externe Quellen, FAQ-Block.
4. Kein `<img>` ohne individuellen Alt-Text; WebP-Auslieferung wo umgestellt.
5. sitemap.xml vollständig + lastmod aktuell; llms.txt ergänzt.
6. Screenshots aller neuen Seiten (Desktop + 390px) an Andreas senden.
7. Manuelle Schritte für Andreas/Willi auflisten: Rich-Results-Test,
   PageSpeed-Check, Search-Console-Alt-URLs einzeln umleiten, Bing-Sitemap,
   HWG-Branch freigeben, echte Testimonials liefern.
