# Sichtbarkeits-Todo für Willi

Diese Aufgaben lassen sich nicht durch Code erledigen – sie brauchen dich
persönlich (Google-Konto, echte Bewertungen, echte Kontakte). Kein
Zeitdruck, aber jeder Haken hier hilft der Website beim Ranking.

- [ ] **Google Business Profile pflegen.** Prüfe, ob dein Eintrag bei Google
  (Karte/„Google Unternehmensprofil") aktuell ist: Öffnungszeiten, Telefon,
  Website-Link, Fotos. Poste ab und zu ein Update oder Foto – das hält das
  Profil aktiv und wirkt sich positiv auf die lokale Auffindbarkeit aus.

- [ ] **Echte Bewertungen sammeln.** Frag Familien, die zufrieden waren, aktiv
  nach einer kurzen Google-Bewertung (ein Link reicht, den schicke ich dir
  gerne fertig). Sobald ein paar echte, schriftliche Bewertungen vorliegen,
  können wir den Testimonial-Bereich auf der Website freischalten (ist
  technisch schon vorbereitet, siehe `index.html`).

- [ ] **Bing Webmaster Tools einrichten.** Kostenloses Gegenstück zur Google
  Search Console. Website dort anmelden und die `sitemap.xml` einreichen –
  bringt zusätzliche Sichtbarkeit bei Bing/Microsoft-Suchen (u. a. auch
  Ausgangspunkt für manche KI-Chatbots).

- [ ] **YouTube.** Falls du mal ein kurzes Video drehst (z. B. „Was ist
  NeuroScanBalance", ein Einblick in eine Lesson), auf YouTube hochladen und
  in der Beschreibung auf die Website verlinken. Videos werden von Google
  oft separat gelistet und bringen zusätzliche Klicks.

- [ ] **Wikidata-Eintrag prüfen/anlegen.** Ein Eintrag auf wikidata.org mit
  Basisdaten (Name, Beruf, Ort, Website) hilft Suchmaschinen, dich als reale
  Person/Unternehmen einzuordnen. Ich kann dir dabei helfen, wenn du magst.
  Sobald die Wikidata-URL steht, bitte mir Bescheid geben: Sie wird dann als
  `sameAs`-Eintrag sowohl beim Person- als auch beim LocalBusiness-Knoten im
  JSON-LD von `index.html` ergänzt (aktuell steht dort nur Instagram bzw.
  Instagram + Google-Knowledge-Graph-Link) – das ist ein kurzer Folge-Task
  für eine spätere `/goal`-Runde.

- [ ] **Markenerwähnungen sammeln.** Wenn andere Websites (Vereine,
  Selbsthilfegruppen, Verzeichnisse) über dich schreiben oder dich verlinken,
  sag mir Bescheid – solche Erwähnungen stärken das Vertrauen der
  Suchmaschinen in die Seite.

- [ ] **Verzeichnis-Einträge NAP-konsistent halten.** NAP = Name, Adresse,
  Telefonnummer. Prüfe bestehende Einträge in Branchenverzeichnissen (z. B.
  Das Örtliche, Gelbe Seiten, 11880) und gleiche sie auf identische Angaben
  ab – bei uns: „Willi Klassen, Auf dem Kampe 2, 49152 Bad Essen,
  +49 151 17 20 03 85". Unterschiedliche Schreibweisen verwirren
  Suchmaschinen.

## Manuelle Schritte für Andreas (nicht Willi)

- [ ] Rich-Results-Test von Google für die wichtigsten Seiten laufen lassen
  (Startseite, 2-3 Blogartikel, kosten.html) – JSON-LD wurde lokal
  validiert, aber der offizielle Test zeigt auch Warnungen/Empfehlungen.
- [ ] PageSpeed-Check nach dem Livegang der neuen Seiten.
- [ ] In der Search Console prüfen, ob alte WordPress-URLs korrekt als
  „verschoben" (301) erkannt werden.
- [ ] Bing-Sitemap einreichen (siehe Willi-Aufgabe oben, technischer Teil).
