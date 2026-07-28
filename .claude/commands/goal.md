# /goal — Nachbesserung nach Re-Audit (86 → 95 Punkte)

Setze die Nachbesserungsliste vom 28.07.2026 (Re-Audit nach dem ersten
SEO-Durchlauf) für neuroscanbalance-badessen.de um. Arbeite die Pakete
**in dieser Reihenfolge** ab. Falls Argumente übergeben wurden (z. B.
`/goal N1 N3`), nur die genannten Pakete ausführen: $ARGUMENTS

Das erste Briefing (71 → 92 Punkte, Pakete 1–6) ist bereits vollständig
umgesetzt und live, inklusive `hwg-wording`-Merge. Diese Datei ersetzt den
alten Plan komplett — nichts von unten überschneidet sich mit dem, was
schon erledigt ist.

## Verbindliche Rahmenregeln (vor dem Start lesen)

1. **CLAUDE.md gilt.** Insbesondere: kein `aggregateRating` ohne sichtbare
   echte Testimonials; Intensiv-Preis nicht auf die Startseite bzw. nicht
   als fester Zahlenwert im Schema; „Intensive" als Wording; nach
   Content-Änderungen `lastmod` in `sitemap.xml`; `llms.txt` synchron halten;
   Preise an ALLEN in CLAUDE.md gelisteten Stellen synchron halten.
2. **Alle neuen/erweiterten sichtbaren Texte durch den entkiisierer-Skill**
   (`.claude/skills/`): keine Fettungen im Fließtext, keine
   Frage-Überschriften-Serien, Dreierketten und Symmetrie brechen,
   Satzlängen 3–40 Wörter mischen. KEINE erfundenen Anekdoten, Zahlen,
   Ausbildungsdetails oder Zitate — fehlende Konkretheit (z. B. genaue
   Ausbildungsdauer, genaue Fallzahlen) als offene Lücke für Willi im
   Abschlussbericht ausweisen statt sie zu erfinden.
3. **Vorab-Check statt Blindübernahme:** Die Nachbesserungsliste wurde von
   einem externen Audit-Tool erzeugt und enthält mindestens eine bekannte
   Ungenauigkeit (falsche Schema-`@id`, siehe Paket N3). Vor jeder Änderung
   den aktuellen Stand der betroffenen Datei lesen und die Vorgabe daran
   spiegeln — nicht Snippets aus der Vorlage 1:1 reinkopieren, ohne zu
   prüfen, ob sie zum bestehenden Code passen.
4. **Design-System wiederverwenden:** Styles nur in `assets/css/style.v3.css`
   ergänzen (nie `style.css`), bestehende Klassen wiederverwenden
   (`.article-content`, `.article-table-wrap`, `.faq-plus`/`.faq-answer`,
   `.article-more`, `.article-cta*`). Keine neuen Farben/Radien erfinden.
5. **Verifizieren vor jedem Push:** Playwright headless
   (`/opt/pw-browsers/chromium-1194/chrome-linux/chrome` via
   `executable_path`), 390px UND 360px, `document.documentElement.scrollWidth`
   ≤ Viewport. Jede JSON-LD-Änderung mit `json.loads()` prüfen. Wortzahl
   je Unterseite per Python zählen (nur sichtbarer Text in
   `.article-content`, keine Nav/Footer/Script-Inhalte).
6. **Deploy:** Commit auf `main` pushen = automatisch live (~1 Min,
   Auto-Tag `live-*`). Kleine, thematisch getrennte Commits je Paket. Diese
   Runde enthält keine rechtlich heiklen Textänderungen — ein separater
   Branch wie beim letzten Mal (`hwg-wording`) ist NICHT nötig, alles kann
   direkt auf `main`.
7. **Abschlussbericht:** Am Ende alle erweiterten/geänderten SICHTBAREN
   Texte auflisten (neue H2-Überschriften je Seite + grobe Wortzahl
   vorher/nachher) + Screenshots der überarbeiteten Seiten senden, damit
   Andreas/Willi sie freigeben oder korrigieren können.

## Bereits gut — nicht anfassen

Laut Re-Audit unverändert lassen: 301-Redirects, 404-Seite, Impressum,
alle 8 Unterseiten-Grundgerüste (H1/Description/Breadcrumb/BreadcrumbList),
Blog-interne-Verlinkung, Vergleichstabelle im Physio-Artikel, WebP-Bilder,
`llms.txt`-Grundgerüst, HWG-Wording.

---

## Paket N1 — Unterseiten inhaltlich ausbauen (größter Hebel)

**Aufwand ca. 3 Std · Wirkung +2 Punkte**

Alle acht neuen Seiten liegen unter der Ziellänge (Thin-Content-Risiko).
Vor dem Schreiben: aktuelle Wortzahl je Seite selbst neu zählen (die Zahlen
unten sind die Werte des Audits vom 28.07., können durch die
Bild-Breite-Fixes danach leicht abweichen — Python-Wortzählung auf den
Text in `.article-content` ansetzen, keine Schätzung).

| Seite | Ziel-Wortzahl | neue H2-Themen |
|---|---|---|
| `ueber-mich.html` | ~700 | „Meine Ausbildung", „Warum ich nur mit Pausen arbeite", „Was ich nicht verspreche" |
| `kinder.html` | ~700 | „Ab welchem Alter?", „Was bringt eine Lesson bei ADHS?", „Wie unterscheidet sich das von Frühförderung?" |
| `erwachsene.html` | ~650 | „Nach dem Schlaganfall: was realistisch geht", „Bei chronischen Schmerzen", „Sturzprophylaxe im Alter" |
| `intensiv-wochenende.html` | ~700 | „Tagesablauf", „Was mitzubringen ist", „Übernachtung und Anfahrt" |
| `ablauf.html` | ~650 | „Was passiert in den ersten fünf Minuten?", „Wie viele Lessons braucht es?", „Was Eltern währenddessen tun" |
| `kosten.html` | ~600 | „Warum die Kasse nicht zahlt", „Was Stiftungen übernehmen und wie man sie anfragt", „Kostenbeispiel für ein Intensive" |
| `neuroscanbalance-minden.html` | ~600 | je ein Absatz: Versorgungslage in der Region (nur wenn belegbar, sonst weglassen), konkretes Anfahrtsbeispiel mit Fahrzeit, regionaler Bezugspunkt |
| `neuroscanbalance-osnabrueck.html` | ~600 | dieselbe Struktur wie Minden, aber eigene Formulierungen |

**Regeln für die Ergänzungen:**

- Jeder neue H2-Block bekommt direkt darunter eine Antwort-Kapsel: erster
  Absatz 45–60 Wörter, beantwortet die Überschrift im ersten Satz, ohne
  Links, isoliert verständlich (Regel steht schon in CLAUDE.md, Paket 4.4
  vom letzten Durchlauf).
- „Kostenbeispiel für ein Intensive" auf `kosten.html`: KEINE konkrete
  Preiszahl nennen (CLAUDE.md-Regel: Intensiv-Preis bleibt „auf Anfrage").
  Stattdessen erklären, wovon der Preis abhängt (Personenzahl, Ort, Umfang)
  — ein Rechenbeispiel ohne Endsumme.
- „Meine Ausbildung" auf `ueber-mich.html`: nur das erweitern, was schon
  belegt ist (Zertifizierung durch NeuroScanBalance-Institut, Trainer- und
  Kindertrainer-Ausbildung). Keine erfundenen Ausbildungsinhalte, -dauer
  oder -orte.
- **Lokalseiten dürfen sich nicht angleichen.** Vor dem Commit einen
  Python-Vergleich der Wortüberlappung zwischen `neuroscanbalance-minden.html`
  und `neuroscanbalance-osnabrueck.html` laufen lassen (einfacher
  Jaccard-Vergleich auf Wortebene reicht) und im Bericht die Überlappung in
  % angeben. Keine identischen Satzblöcke zwischen beiden Seiten.

## Paket N2 — Interne Verlinkung auf den Unterseiten

**Aufwand ca. 1 Std · Wirkung +1 Punkt**

Aktuell: `kosten.html` 1 Link, beide Lokalseiten 0 Links im Fließtext
(Stand Audit — vor dem Commit den IST-Zustand neu prüfen, da bei den
Lokalseiten inzwischen z. B. `kinder.html`/`erwachsene.html`-Links stehen
könnten).

- Pro Unterseite **3–5 kontextuelle Links im Fließtext**, beschreibender
  Ankertext (nicht „mehr dazu hier").
- „Passt dazu"-Block (= bestehende `.article-more`-Klasse) mit 3 manuell
  gewählten, thematisch verwandten Zielen pflegen/ergänzen.

Verlinkungsmatrix (Dateinamen aus dem Repo, nicht die Kurznamen aus der
Vorlage):

```
kosten.html                    → ablauf.html, intensiv-wochenende.html, blog/ablauf-einzel-lesson-und-intensiv-wochenende.html
ablauf.html                    → kosten.html, kinder.html, blog/was-ist-neuroscanbalance.html
kinder.html                    → ablauf.html, intensiv-wochenende.html, blog/mein-kind-entwickelt-sich-langsamer.html
erwachsene.html                → ablauf.html, kosten.html, blog/was-ist-neuroscanbalance.html
intensiv-wochenende.html       → ablauf.html, kosten.html, blog/ablauf-einzel-lesson-und-intensiv-wochenende.html
ueber-mich.html                → ablauf.html, kinder.html, blog/was-ist-neuroscanbalance.html
neuroscanbalance-osnabrueck.html / -minden.html → ablauf.html, kosten.html, kinder.html
```

Zusätzlich: von der Startseite und aus mindestens einem passenden
Blogartikel je einen Link **auf** beide Lokalseiten setzen (prüfen, ob das
aus dem letzten Durchlauf schon existiert — im `maps-region`-Absatz von
`index.html` stehen bereits Links auf beide Lokalseiten; falls das
weiterhin die einzigen eingehenden Links sind, zusätzlich aus einem
Blogartikel heraus verlinken, z. B. `blog/neuroscanbalance-bei-kindern.html`
oder `blog/was-ist-neuroscanbalance.html`).

## Paket N3 — Service-Schema auf die Angebotsseiten

**Aufwand ca. 20 Min · Wirkung +1 Punkt**

`Service`-Schema liegt bisher nur auf der Startseite (`#service-lesson`,
`#service-intensive`). Zusätzlich auf `ablauf.html`, `kosten.html` und
`intensiv-wochenende.html` ins jeweilige `@graph` einfügen.

**Korrektur zur Vorlage:** Die Beispiel-Snippets aus der Nachbesserungsliste
verweisen mit `provider.@id` auf `#localbusiness` — das ist FALSCH, der
echte LocalBusiness-Knoten in `index.html` heißt `#business`. Immer
`"provider": {"@id": "https://neuroscanbalance-badessen.de/#business"}`
verwenden, sonst entsteht ein zweiter, unverbundener Graph-Eintrag.

- `kosten.html`: zwei Service-Knoten — Einzel-Lesson (mit `offers.price`
  „80.00" EUR) und Intensive-Wochenende (mit `priceSpecification`-Text
  „Preis auf Anfrage, abhängig von Umfang und Ort", KEIN numerischer Preis).
- `ablauf.html`: ein Service-Knoten für die Einzel-Lesson (gleiche Struktur
  wie auf der Startseite, `@id` mit `#service-lesson-ablauf` o. ä., damit
  keine doppelte `@id` quer über die Seiten entsteht — jede Seite braucht
  ihre eigene eindeutige `@id`, auch wenn der Inhalt ähnlich ist).
- `intensiv-wochenende.html`: ein Service-Knoten mit `priceSpecification`
  wie bei `kosten.html`.
- Jeweils `serviceType`, `areaServed` (Osnabrücker Land) und `audience`
  wie in der Vorlage übernehmen, mit `provider`-Korrektur wie oben.

## Paket N4 — Vergleichstabelle auf /kosten.html

**Aufwand ca. 30 Min · Wirkung +1 Punkt**

Als echte `<table>` in `.article-table-wrap` (Klasse existiert schon,
CSS ist bereits auf Textbreite gefixt — einfach wiederverwenden, nicht neu
bauen).

Spalten: **Format · Dauer · Umfang · Preis · geeignet für**
Zeilen: **Einzel-Lesson · Intensive-Wochenende · Folgetermine**

„Folgetermine" = einzelne Lessons nach einem abgeschlossenen Intensive
oder zwischen zwei Intensives, gleiches Format/gleicher Preis wie die
Einzel-Lesson, nur mit dem Zusatz „nach Bedarf" in der Spalte „Umfang".
Intensive-Preis in der Tabelle als „auf Anfrage" (kein Zahlenwert).

## Paket N5 — Bild-Ladepriorität prüfen und nachziehen

**Aufwand ca. 5–15 Min · Wirkung +0,5 Punkte**

Die Vorlage nennt `assets/img/willi-klassen-portrait.webp` als
„Hero-Bild" mit fehlendem `fetchpriority` — das ist ungenau: Das echte
LCP-Element der Startseite ist der Hero-Slider-Hintergrund
(`assets/img/hero/slide-1.webp`), der bereits per
`<link rel="preload" as="image" fetchpriority="high">` im `<head>` von
`index.html` priorisiert wird (aus dem ersten Durchlauf). Diesen Preload
NICHT doppeln oder auf die Portrait-Datei umbiegen.

Tatsächliche Lücke: Die `.article-hero-img`-Bilder auf den Blogartikeln
und auf `ueber-mich.html` (jeweils das erste sichtbare Bild der Seite,
kein `loading="lazy"`, aber auch kein `fetchpriority`/`decoding`) haben
noch keine explizite Ladepriorität. Auf allen Seiten mit
`<img class="article-hero-img" ...>` ergänzen:

```html
<img class="article-hero-img" src="..." alt="..." width="1600" height="900"
     fetchpriority="high" decoding="async">
```

Alle anderen Bilder (Logo im Footer, About-Badge etc.) behalten
`loading="lazy"` wie bisher — nur das jeweils erste, große Bild pro Seite
bekommt die Priorität.

## Paket N6 — robots.txt: Google-Agent ergänzen

**Aufwand ca. 5 Min · Wirkung +0,5 Punkte**

Aktuellen Aufbau von `robots.txt` zuerst lesen (bestehendes Muster aus
freigegebenen Bots übernehmen), dann ergänzen:

```
User-agent: Google-Agent
Allow: /
```

An der Stelle einfügen, wo die anderen namentlich freigegebenen Bots
stehen (GPTBot, ClaudeBot, …), nicht ans Dateiende ohne Kontext.

---

## Vorbereitet, aber nicht in diesem Durchlauf — Testimonials

Bereits erledigt (Paket 5 vom letzten Durchlauf): Schema-Vorlage für
`aggregateRating`/`review` liegt als HTML-Kommentar in `index.html`,
Testimonial-Bereich ist auskommentiert. Kein weiterer Code-Task hier.
Zwei Regeln gelten weiter, sobald Willi Freigaben hat (nicht jetzt
umsetzen, nur als Erinnerung im Abschlussbericht wiederholen):

1. Nur Bewertungen ins Schema, die auch sichtbar auf der Seite stehen.
2. Realistische Bewertung (4,7–4,9) statt glatter 5,0 verwenden.

## Nicht Claude-Aufgabe — für docs/sichtbarkeit-todo-willi.md ergänzen

Diese Punkte NICHT selbst umsetzen, sondern als neuen Punkt in die
bestehende `docs/sichtbarkeit-todo-willi.md` eintragen (Datei existiert
schon aus dem letzten Durchlauf, nur ergänzen, nicht neu anlegen):

- Wikidata-Eintrag für Willi/die Praxis anlegen. Sobald eine Wikidata-URL
  existiert, sie in `sameAs` BEIDER Schema-Knoten ergänzen (`#business`
  UND `#willi` in `index.html` — aktuell steht dort nur Instagram bzw.
  Instagram + Google-Knowledge-Graph-Link). Das ist ein Folge-Task für
  eine spätere `/goal`-Runde, sobald die URL feststeht.
- Bewertungen sammeln, Bing Webmaster Tools, Google Business Profile,
  YouTube, Markenerwähnungen: stehen schon in `docs/sichtbarkeit-todo-willi.md`,
  nur gegenprüfen, dass nichts fehlt.

## Abnahme (am Ende ausführen und Ergebnis berichten)

1. Keine Unterseite unter 600 Wörtern (Python-Wortzählung, Werte im
   Bericht auflisten: vorher/nachher je Seite).
2. Jede Unterseite hat mindestens 3 interne Links im Fließtext
   (automatisch zählen wie beim letzten Durchlauf).
3. Lokalseiten: Wortüberlappung neu berechnen und im Bericht angeben,
   keine identischen Satzblöcke.
4. `Service`-Schema auf `ablauf.html`, `kosten.html`,
   `intensiv-wochenende.html` vorhanden, `provider.@id` = `#business`,
   jede Seite hat eine eigene, eindeutige `@id` je Service-Knoten.
5. Vergleichstabelle auf `kosten.html` ist eine echte `<table>` in
   `.article-table-wrap`, kein Overflow bei 390/360px.
6. `fetchpriority="high" decoding="async"` auf allen
   `.article-hero-img`-Bildern, `loading="lazy"` bleibt auf allen anderen
   Bildern erhalten.
7. `Google-Agent` in `robots.txt`.
8. `llms.txt` spiegelt die erweiterten Inhalte wider (falls neue Fakten
   dazukommen, z. B. Stiftungs-Details).
9. `sitemap.xml`-`lastmod` aller geänderten Seiten aktualisiert.
10. JSON-LD aller geänderten Seiten mit `json.loads()` validiert.
11. Screenshots aller inhaltlich erweiterten Seiten (Desktop + 390px) an
    Andreas senden.
12. Manuelle Schritte für Andreas/Willi im Bericht auflisten: Rich-Results-
    Test, PageSpeed-Check, Wikidata-Eintrag (siehe oben).
