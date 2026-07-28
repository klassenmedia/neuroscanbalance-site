# /goal — Pillar-Seite als Content-Hub

Baue eine zentrale Pillar-Seite für neuroscanbalance-badessen.de, die alle
bestehenden Blogartikel und Themen-Unterseiten als Cluster bündelt und in
beide Richtungen verlinkt. Arbeite die Pakete **in dieser Reihenfolge** ab.
Falls Argumente übergeben wurden (z. B. `/goal P1 P3`), nur die genannten
Pakete ausführen: $ARGUMENTS

Alle vorherigen Runden (SEO-Briefing Pakete 1–6, Nachbesserung N1–N6,
Restliste) sind bereits umgesetzt und live. Diese Datei ersetzt den alten
`/goal`-Plan komplett.

## Warum das Ganze (Kontext für die Umsetzung)

Eine Pillar-Seite ist eine sehr ausführliche Seite zu einem Oberthema
(hier: NeuroScanBalance insgesamt), die alle Unterthemen — die 7
Blogartikel plus die 8 Themen-Unterseiten — an einem Ort zusammenfasst und
in beide Richtungen verlinkt. Zwei Effekte: (1) signalisiert Google
„topische Autorität" für das gesamte Themencluster statt nur für einzelne
Seiten, (2) KI-Suchtools (ChatGPT, Perplexity, Google AI Overviews)
zitieren bevorzugt eine einzelne, in sich geschlossene Seite statt
Informationen aus mehreren Einzelartikeln zusammenzusuchen.

## Verbindliche Rahmenregeln (vor dem Start lesen)

1. **CLAUDE.md gilt.** Insbesondere: kein `aggregateRating` ohne sichtbare
   echte Testimonials; Intensiv-Preis nicht als fester Zahlenwert; „Intensive"
   als Wording; `lastmod` in `sitemap.xml` aktualisieren; `llms.txt`
   synchron halten; Preisnennungen an ALLEN dort gelisteten Stellen synchron.
2. **entkiisierer-Skill** (`.claude/skills/`) auf den GESAMTEN neuen Text
   anwenden: keine Fettungen im Fließtext, keine Frage-Überschriften-Serien,
   Dreierketten/Symmetrie brechen, Satzlängen 3–40 Wörter mischen.
3. **Kein Copy-Paste, sondern Synthese.** Die Pillar-Seite fasst zusammen
   und verlinkt weiter, sie kopiert keine ganzen Absätze aus den
   Einzelseiten. Sonst entsteht Duplicate Content, der dem Cluster eher
   schadet als hilft. Jeder Abschnitt: eigene, verdichtete Formulierung,
   Detail bleibt auf der Zielseite.
4. **Keine neuen Fakten erfinden.** Nur zusammenfassen, was auf den
   bestehenden Seiten (index.html, Blogartikel, Unterseiten, llms.txt)
   bereits steht. Keine neuen Zahlen, Zitate oder Behauptungen.
5. **Design-System wiederverwenden:** `.article-content`, `.breadcrumb`,
   `.article-cta*`, `.faq-plus`/`.faq-answer`, `.article-table-wrap` falls
   passend. Keine neuen Farben/Radien. Ein einfaches Inhaltsverzeichnis
   (Sprungmarken-Liste) ist ok, aber mit den vorhandenen Typografie-Klassen
   umsetzen, kein neues Widget-System bauen.
6. **Verifizieren vor jedem Push:** Playwright headless
   (`/opt/pw-browsers/chromium-1194/chrome-linux/chrome` via
   `executable_path`), 390px UND 360px, `document.documentElement.scrollWidth`
   ≤ Viewport. JSON-LD mit `json.loads()` prüfen. Wortzahl der Pillar-Seite
   per Python zählen (Ziel: 3.000+ Wörter im sichtbaren `.article-content`).
7. **Deploy:** Commit auf `main` pushen = automatisch live. Kleine,
   thematisch getrennte Commits je Paket.
8. **Abschlussbericht:** Am Ende die neue Seite (Titel, H1, Struktur,
   Wortzahl) zusammenfassen + Screenshots (Desktop + 390px) senden, damit
   Andreas/Willi sie freigeben oder korrigieren können.

## Paket P1 — Struktur & Content der Pillar-Seite

**Datei:** `neuroscanbalance-ratgeber.html` (root-level, gleiches
Kopf-Schema wie die anderen Unterseiten: preconnects, favicons,
theme-color, canonical, OG/Twitter, Matomo).

**Title (≤ 60 Zeichen):** `NeuroScanBalance Ratgeber: Methode, Ablauf, Kosten`
**Description (150–160 Zeichen):** eigene, zusammenfassende Beschreibung
nach demselben Muster wie die anderen Unterseiten (Python-Zeichenzählung
im Bericht ausgeben).
**H1:** etwas wie „NeuroScanBalance: Der Ratgeber — Methode, Ablauf, Kosten
und für wen sie geeignet ist" (final in Willis Tonfall entkiisiert
formulieren, nicht wörtlich übernehmen).

**Aufbau (jeder Abschnitt 300–500 Wörter, mit Antwort-Kapsel direkt nach
der H2, plus mindestens ein Link auf die vertiefende Zielseite):**

1. **Einleitung** (kurz, kein H2): worum es auf dieser Seite geht, dass sie
   als Wegweiser zu allen Themen dient.
2. **Inhaltsverzeichnis:** einfache Liste mit Sprungmarken (`<a href="#...">`)
   zu den H2-Abschnitten darunter — reine Navigationshilfe, keine neue CSS.
3. **Was ist NeuroScanBalance?** — Kurzfassung der Methode (Neuroplastizität,
   sanfte Impulse, schmerzfrei) → Link auf `blog/was-ist-neuroscanbalance.html`.
4. **Für Kinder geeignet** — Zielgruppen/Diagnosen zusammengefasst → Links
   auf `kinder.html`, `blog/neuroscanbalance-bei-kindern.html`,
   `blog/mein-kind-entwickelt-sich-langsamer.html`,
   `blog/zerebralparese-wege-neben-der-klassischen-therapie.html`,
   `blog/muskelhypotonie-baby-niedriger-muskeltonus.html`.
5. **Für Erwachsene geeignet** — → Link auf `erwachsene.html`.
6. **NeuroScanBalance oder Physiotherapie?** — Einordnung/Abgrenzung → Link
   auf `blog/neuroscanbalance-oder-physiotherapie.html`.
7. **Wie eine Lesson und ein Intensive ablaufen** — Einzel-Lesson,
   Intensiv-Wochenende, Integrationspause/goldene Regel zusammengefasst →
   Links auf `ablauf.html`, `intensiv-wochenende.html`,
   `blog/ablauf-einzel-lesson-und-intensiv-wochenende.html`.
8. **Was NeuroScanBalance kostet** — Preise, Selbstzahlerleistung,
   Stiftungen, KEINE Intensiv-Preiszahl → Link auf `kosten.html`.
9. **Über Willi Klassen und den Standort** — kurz, mit Links auf
   `ueber-mich.html`, `neuroscanbalance-osnabrueck.html`,
   `neuroscanbalance-minden.html`.
10. **Häufige Fragen** — 5–6 NEUE, übergreifende Fragen (nicht 1:1 aus
    anderen Seiten kopiert, siehe Paket P3 für Schema), `<details>`-Blöcke
    wie auf den Blogartikeln.
11. **Abschluss-CTA** (bestehende `.article-cta`-Klassen): Termin
    vereinbaren + Link auf `anmeldung.html`.

Ziel-Gesamtlänge: 3.000+ Wörter im sichtbaren Text. Nach dem Schreiben mit
Python zählen und im Bericht angeben.

## Paket P2 — Interne Verlinkung in beide Richtungen

**Von der Pillar-Seite weg:** siehe Linkliste in P1 — jeder Cluster-Artikel/
jede Unterseite wird mindestens einmal verlinkt.

**Zur Pillar-Seite hin:** Das ist der eigentliche Hebel und darf nicht
fehlen. In JEDEM der 7 Blogartikel (+ 2 Drafts) und JEDER der 8
Themen-Unterseiten einen Fließtext-Link auf `neuroscanbalance-ratgeber.html`
ergänzen, mit beschreibendem Ankertext (z. B. „im NeuroScanBalance-Ratgeber"
oder „im großen Ratgeber"), nicht mechanisch überall denselben Satz
copy-pasten — leicht variieren.

Zusätzlich:
- Footer-Navigation auf `index.html` („Mehr erfahren"-Liste, bereits
  vorhanden) um einen Link auf den Ratgeber ergänzen.
- `blog/index.html`: ein Hinweis-Absatz oder Link oben auf der Blog-Übersicht.

## Paket P3 — Schema

- `WebPage` + `BreadcrumbList` im `@graph`, wie bei den anderen
  Unterseiten (`about` verweist auf `#business`).
- `FAQPage`-Schema für die 5–6 neuen FAQ-Fragen aus P1, Punkt 10 — nur
  Fragen, die auch sichtbar auf der Seite stehen.
- Kein `Article`/`TechArticle`-Schema zusätzlich nötig — das würde mit den
  bestehenden `BlogPosting`-Knoten der Einzelartikel konkurrieren.

## Paket P4 — sitemap.xml & llms.txt

- Neue URL in `sitemap.xml` mit `lastmod` = heutiges Datum, `priority`
  hoch ansetzen (0.9, da zentrale Hub-Seite, niedriger nur als die
  Startseite).
- `llms.txt`: unter „Weitere Seiten" ergänzen, möglichst als ersten
  Eintrag oder gesondert hervorgehoben, da es sich um die Übersichtsseite
  handelt.

## Abnahme (am Ende ausführen und Ergebnis berichten)

1. Wortzahl der Pillar-Seite ≥ 3.000 (Python-Zählung, Wert im Bericht).
2. Title ≤ 60 Zeichen, Description 150–160 Zeichen (Python-Zeichenzählung).
3. Genau eine H1, sichtbare Breadcrumb, valides JSON-LD (`json.loads()`).
4. Jeder Cluster-Artikel/jede Unterseite ist von der Pillar-Seite aus
   verlinkt UND verlinkt selbst zurück auf die Pillar-Seite (Python-Check:
   für jede Zieldatei aus der Liste in P1/P2 pruefen, ob der Link in beide
   Richtungen existiert).
5. Kein Overflow bei 390/360px auf der neuen Seite.
6. `sitemap.xml` und `llms.txt` aktualisiert.
7. Screenshots der neuen Seite (Desktop + 390px) an Andreas senden.
8. Kurzer Hinweis im Bericht, falls Inhalte aus Platz- oder
   Konsistenzgründen gekürzt wurden, damit Andreas/Willi das freigeben kann.
