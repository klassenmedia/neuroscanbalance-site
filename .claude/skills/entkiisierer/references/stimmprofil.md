# Stimmprofile: für wen wird geschrieben

Ein entkiisierter Text ohne Stimme ist sauber und trotzdem tot. Dieser Teil entscheidet, ob
der Text nach einem Menschen klingt oder nur nach keinem Roboter.

## Wann welches Verfahren

| Situation | Vorgehen |
|---|---|
| Profil existiert in `profiles/<name>.md` | Laden, befolgen, fertig |
| Textproben liegen vor (Chat, Datei, Website) | Analyse nach Abschnitt "Analyse", danach Profil anbieten |
| Nur Kontext bekannt (Branche, Kanal, Zielgruppe) | Stimme ableiten, Annahme offenlegen |
| Nichts bekannt | Eine kurze Rückfrage, dann schreiben |

## Analyse einer Textprobe

Mindestens 300 Wörter echtes Material, besser zwei bis drei verschiedene Texte. Notiere:

1. **Anrede und Register.** Du oder Sie? Ihr? Duzt die Person auch Fremde? Wie förmlich sind
   Grußformeln?
2. **Satzlänge und Rhythmus.** Durchschnitt und Schwankung. Gibt es Ein-Wort-Sätze?
   Schachtelsätze?
3. **Wortwahl-Niveau.** Umgangssprache, Fachsprache, Mischung. Sagt die Person "Sachen" oder
   "Aspekte"? "Kunden" oder "Klienten"?
4. **Lieblingswörter und Tics.** Wiederkehrende Formulierungen, Füllwörter ("halt", "eben",
   "ganz ehrlich"), Standardeinstiege.
5. **Interpunktion.** Klammern? Doppelpunkte? Gedankenstriche? Ausrufezeichen? Wie oft
   Absätze?
6. **Übergänge.** Explizite Konnektoren oder harte Schnitte?
7. **Haltung.** Wie deutlich wird bewertet? Wird widersprochen? Wird gescherzt? Ironie?
8. **Ich oder wir.** Spricht die Person als Person oder als Firma?
9. **Fachtiefe.** Werden Begriffe erklärt oder vorausgesetzt?
10. **Was fehlt.** Was benutzt die Person auffällig *nicht* (keine Emojis, keine Anglizismen,
    keine Superlative)? Das ist oft prägender als das, was sie benutzt.

Regionale und generationsbedingte Eigenheiten mit übernehmen, wenn sie in der Probe
auftauchen. Nicht künstlich hinzuerfinden.

## Profil anlegen

Nach der Analyse anbieten, das Profil zu sichern. Dateiname: Vorname oder Rolle, klein und
mit Bindestrich, z.B. `profiles/andy.md`, `profiles/kunde-mueller-gmbh.md`.

Vorlage:

```markdown
# Stimmprofil: <Name oder Rolle>

Gilt für: <Kanäle, z.B. LinkedIn, Kundenmails, Website>
Sprache: <Deutsch / Englisch / beides>

## Anrede
Du / Sie / Ihr. Grußformel: <...>

## Satzbau
Durchschnittlich <x> Wörter, starke Schwankung / gleichmäßig.
Besonderheiten: <...>

## Wortwahl
Benutzt: <typische Wörter>
Benutzt nie: <Tabuwörter>
Fachbegriffe: erklärt / setzt voraus

## Haltung
<bewertet deutlich / bleibt neutral / ironisch / sachlich>

## Formales
Emojis: ja/nein. Listen: ja/nein. Absatzlänge: <...>
Interpunktion: <...>

## Typische Einstiege
- <Beispiel 1>
- <Beispiel 2>

## Beispielabschnitt (Originalzitat)
> <5-8 Zeilen echter Text der Person als Referenz>
```

Der Beispielabschnitt am Ende ist der wichtigste Teil. Ein echtes Zitat schlägt jede
Beschreibung.

## Anwendung beim Schreiben

- Muster entfernen und Stimme einsetzen sind ein Arbeitsschritt, nicht zwei. Nicht erst
  neutralisieren und dann Persönlichkeit draufkleben.
- Wenn die Person kurze Sätze schreibt, keine langen produzieren. Wenn sie "Zeug" sagt, nicht
  auf "Komponenten" hochstufen.
- Fehlt ein Profil, ist die neutrale Grundstimme: direkt, konkret, wechselnder Satzrhythmus,
  eine erkennbare Meinung, keine Werbesprache.
- Beim Schreiben für eine fremde Person keine Meinungen, Erlebnisse oder Zahlen erfinden.
  Lieber eine Lücke markieren als eine Biografie halluzinieren.

## Mehrere Stimmen im selben Projekt

Wenn der User für verschiedene Kunden schreibt: vor dem Schreiben klären, für wen. Wenn
unklar und mehrere Profile existieren, mit AskUserQuestion die Profile zur Auswahl stellen
statt zu raten.
