---
name: entkiisierer
description: |
  EntKIsiert Texte - entfernt alle typischen Merkmale KI-generierter Sprache und macht
  Texte menschlich klingend. Zweisprachig (Deutsch + Englisch, erkennt die Sprache
  automatisch) und passt sich an die Stimme der Person an, für die der Text geschrieben wird.
  NUTZE DIESEN SKILL IMMER in zwei Fällen:
  (1) MANUELL, wenn der User sagt: "entkiisiere das", "/entkiisieren", "klingt nach KI",
  "mach das menschlicher", "humanize this", "AI-Wörter raus", "das liest sich wie ChatGPT",
  "prüf den Text auf KI-Sprache", "so schreibt kein Mensch".
  (2) AUTOMATISCH als letzter Arbeitsschritt, bevor ein längerer Text ausgeliefert wird, den
  der User selbst veröffentlicht oder verschickt: LinkedIn-Posts, Blogartikel, Newsletter,
  Website-Texte, Landingpages, Angebote, Mails an Kunden, Pressetexte, Skripte, Bewerbungen,
  Whitepaper, Social-Media-Captions - auch wenn der User nicht danach fragt.
  NICHT automatisch anwenden bei: Code, Doku, Chat-Antworten, Notizen, Analysen, Listen,
  Zusammenfassungen für den User selbst.
license: MIT
allowed-tools:
  - Read
  - Write
  - Edit
  - Grep
  - Glob
  - AskUserQuestion
---

# EntKIsierer

Ziel: Der Text soll nicht "KI-frei geprüft" wirken, sondern klingen, als hätte ihn ein
bestimmter Mensch geschrieben. Muster entfernen ist die halbe Arbeit. Die andere Hälfte ist,
eine echte Stimme einzusetzen.

## Ablauf

1. **Sprache erkennen.** Deutsch → `references/marker-de.md` lesen. Englisch →
   `references/marker-en.md` lesen. Gemischt → beide.
2. **Stimme klären** (siehe unten). Ohne Stimme wird der Text sauber, aber seelenlos.
3. **Erste Fassung schreiben.** Muster entfernen, Stimme einsetzen.
4. **Selbstaudit.** Frage Dich: "Woran erkennt man an diesem Text trotzdem noch, dass er von
   einer KI stammt?" Beantworte das ehrlich in 2-4 Stichpunkten. Erfahrungsgemäß findet
   dieser Schritt genau die Fehler, die sonst durchrutschen - vor allem erfundene Details,
   die man eingebaut hat, damit der Text konkreter wirkt.
5. **Endfassung.** Die Punkte aus Schritt 4 beheben. Das ist der Text, der ausgeliefert wird.

Schritt 4 nie überspringen. Der Unterschied zwischen Fassung 1 und Fassung 2 ist der
eigentliche Wert dieses Skills.

## Stimme klären

Der Text gehört fast nie Dir. Er gehört dem User, seinem Kunden, oder einer Person, in deren
Namen geschrieben wird. Kläre vor dem Schreiben:

**Für wen wird geschrieben?** Prüfe in dieser Reihenfolge:

1. Gibt es in `profiles/` ein passendes Stimmprofil? (`profiles/<name>.md`) → laden und
   befolgen.
2. Hat der User in diesem Chat Textproben der Person geliefert oder liegen frühere Texte
   von ihr vor? → analysieren nach dem Verfahren in `references/stimmprofil.md`, anwenden,
   und danach anbieten, das Profil unter `profiles/<name>.md` zu sichern.
3. Gibt es im Kontext genug Signal (Branche, Zielgruppe, Kanal, bisherige Chatsprache
   des Users)? → daraus eine plausible Stimme ableiten und die Annahme im Fazit offenlegen,
   z.B. "Ich habe geduzt und eher knapp geschrieben - sag Bescheid, wenn Du förmlicher willst."
4. Nichts davon vorhanden → **eine** kurze Rückfrage (AskUserQuestion): Wer spricht, wen
   spricht er an, wie förmlich. Nicht mehr fragen.

**Ehrlichkeitsregel:** Beim Schreiben für fremde Personen keine erfundenen persönlichen
Anekdoten, keine erfundenen Meinungen, keine erfundenen Zahlen einbauen, nur um den Text
menschlich wirken zu lassen. Menschlichkeit entsteht durch Rhythmus, Konkretheit und Haltung -
nicht durch erfundene Biografie. Wenn dem Text konkrete Details fehlen, benenne die Lücke
("Hier fehlt ein echtes Beispiel aus Deiner Praxis") statt sie zu erfinden.

## Die sechs Kernbewegungen

Sprachunabhängig. Die Detaillisten stehen in den Referenzdateien.

1. **Bedeutung entrümpeln.** Streiche jeden Satz, der nur behauptet, dass etwas wichtig,
   wegweisend oder Teil eines größeren Trends ist. Wenn nach dem Streichen keine Information
   fehlt, war es Füllstoff.
2. **Konkret statt allgemein.** Ersetze Adjektive durch Fakten. "Beeindruckendes Wachstum" →
   "von 4 auf 11 Mitarbeiter in zwei Jahren". Wenn keine Zahl vorliegt: Adjektiv streichen,
   nicht ersetzen.
3. **Rhythmus brechen.** KI schreibt gleichlange Sätze und gleichlange Absätze. Mische kurz
   und lang. Ein Dreiwortsatz darf sein. Ein Absatz aus einem Satz auch.
4. **Haltung zeigen.** Menschen bewerten, zweifeln, widersprechen sich leicht. "Das
   funktioniert, aber es nervt beim Einrichten" ist menschlicher als "Das bietet Vorteile
   und Herausforderungen."
   Schreibst Du im Namen einer fremden Person oder Firma, gilt: Haltung ja, erfundene Meinung
   nein. Erlaubt ist Haltung, die aus dem Ausgangstext oder den Fakten folgt - eine
   Einschränkung benennen, eine Übertreibung zurücknehmen, sagen wofür etwas *nicht* taugt.
   Nicht erlaubt sind neue Wertungen, die die Person so nie geäußert hat.
5. **Struktur auflösen.** Nicht alles muss eine Liste sein, nicht jede Liste braucht fette
   Stichwörter mit Doppelpunkt. Fließtext ist der Normalfall, Listen die Ausnahme.
6. **Symmetrie zerstören.** Drei Beispiele, drei Adjektive, drei Absätze, jeder Abschnitt
   mit Mini-Fazit: Das ist die deutlichste KI-Signatur überhaupt. Zwei oder vier tun es auch.

## Referenzdateien

- `references/marker-de.md` - deutsche KI-Marker: Floskeln, Wortlisten, Satzmuster,
  Nominalstil, Typografie. Bei deutschen Texten immer lesen.
- `references/marker-en.md` - englische KI-Marker nach Wikipedia "Signs of AI writing".
  Bei englischen Texten immer lesen.
- `references/stimmprofil.md` - Verfahren zur Stimmanalyse und Profilvorlage.

## Was nicht passieren darf

- **Keine Fakten ändern.** Zahlen, Namen, Aussagen, Fachbegriffe bleiben wie sie sind.
  EntKIsieren ist Umformulieren, nicht Umschreiben des Inhalts.
- **Nicht überkorrigieren.** Ein Gedankenstrich ist erlaubt. Drei Beispiele sind manchmal
  einfach richtig. "Wichtig" ist kein verbotenes Wort. Ziel ist natürliche Sprache, nicht
  das Umgehen eines Detektors.
- **Fachsprache bleibt.** In juristischen, medizinischen oder technischen Texten sind
  Nominalstil und Passiv oft korrekt. Dort nur Floskeln entfernen, nicht den Duktus.
- **Keine künstliche Lässigkeit.** Aus einem seriösen Angebotstext keinen Instagram-Post
  machen. Register beibehalten.
- **Länge.** Der entkiisierte Text wird kürzer, oft deutlich. Das ist kein Fehler. Streichen
  darfst Du alles, was keine Information trägt - auch ganze Absätze. Nicht streichen darfst
  Du Inhalt, nur weil er sich schwer umformulieren lässt.

## Sonderfall: der Text sagt gar nichts

Häufigster Fall bei Werbe- und Social-Media-Texten: Nach dem Entfernen aller Muster bleibt
keine überprüfbare Information übrig - keine Zahl, kein Name, kein Datum, kein Beispiel.

Dann ist EntKIsieren allein zu wenig, und Du sagst das auch. Vorgehen:

1. Den Text so weit entkiisieren, wie es ohne neue Fakten geht.
2. Nichts erfinden, um die Lücke zu füllen. Keine erfundenen Kundenzitate, keine
   Beispielzahlen, keine plausiblen Platzhalter.
3. **Pflicht:** Am Ende konkret auflisten, welche zwei bis drei Angaben der Text braucht, um
   zu funktionieren ("Welcher Betrieb, welches Gewerk, wie viele Stunden gespart?"). Diese
   Liste ist in diesem Fall kein optionaler Zusatz, sondern Teil der Lieferung - auch bei
   automatischer Anwendung.

## Ausgabeformat

**Bei manuellem Aufruf** (User bittet ausdrücklich um EntKIsierung):

1. Die Endfassung des Textes
2. "Was noch nach KI klang" - die 2-4 Punkte aus dem Selbstaudit, jeweils mit dem Beispiel
3. Ein bis zwei Sätze, was am Text jetzt noch fehlt, um wirklich von der Person zu sein
   (z.B. ein echtes Beispiel, eine Zahl, eine Meinung)

**Bei automatischer Anwendung** (Text wurde ohnehin gerade geschrieben):

Nur den fertigen Text ausliefern. Kein Bericht, keine Musterliste, keine Erwähnung des
Skills. Der Prozess läuft still im Hintergrund. Höchstens ein Satz am Ende, falls dem Text
etwas Konkretes fehlt, das nur der User liefern kann.

## Vorlesetest

Bei Zweifeln an einer einzelnen Stelle: Lies sie im Kopf laut und frag Dich, ob ein Mensch
das so in einer Sprachnachricht sagen würde. Wenn nein, umschreiben.
(Die vollständige Prüfliste für den ganzen Text steht am Ende der jeweiligen Referenzdatei.)
