# Anmeldeformular „Intensive" – Spezifikation (Entwurf / noch nicht umsetzen)

**Quelle:** Sprachnachricht Willi Klassen, 16.07.2026 (Transkript unten).
**Status:** 📌 nur festgehalten. Willi ausdrücklich: „Da geht's noch **nicht** darum" –
also **nicht jetzt bauen**, erst wenn freigegeben.

## Idee / Kontext
Perspektivisch soll man auf der Seite auf einen Intensive-Termin klicken und sich
über ein **Online-Anmeldeformular** verbindlich anmelden können (später evtl. mit
automatischer E-Mail/Anfrage an die Trainer). Diese Notiz hält die von Willi
genannten Formularfelder fest.

## Benötigte Felder

### Angaben zu Eltern / Erziehungsberechtigten (für Rechnung & Kontakt)
- Vorname
- Nachname
- Elternteil (welcher Elternteil / Erziehungsberechtigter)
- Straße und Hausnummer
- Postleitzahl und Ort
- Telefonnummer
- E-Mail-Adresse

### Angaben zum Kind
- Name des Kindes
- Geburtsdatum des Kindes
- Alter des Kindes
- Diagnose *(freiwillig / optional)*

### Einverständniserklärung (Text im Formular)
> „Hiermit melde ich mein Kind verbindlich zum NeuroScanBalance Intensive an.
> Ich bestätige, dass die angegebenen Daten korrekt sind."

### Ort / Datum / Unterschrift
- Bei einem Online-Formular **nicht notwendig** (laut Willi) – entfällt.

## Offene Punkte vor der Umsetzung (wichtig)
- **Datenschutz / DSGVO:** Die (freiwillige) Diagnose ist eine **Gesundheitsangabe**
  = besondere Kategorie personenbezogener Daten (Art. 9 DSGVO). Braucht eine
  ausdrückliche Einwilligungs-Checkbox + Hinweis in der Datenschutzerklärung,
  sichere Übertragung und klare Zweckbindung.
- **Wohin geht die Anmeldung?** E-Mail an Willi (und ggf. Tanja Janzen)? Zusätzlich
  Bestätigungs-Mail an die Eltern?
- **Double-Opt-in / Spamschutz** (z. B. einfaches Honeypot-Feld).
- **Pflichtfelder vs. optionale Felder** festlegen.
- **Technik:** statische Seite ohne Backend → Versand z. B. über einen
  Formular-Dienst oder ein kleines Server-Script; muss zu All-Inkl passen.

## Rohtranskript
```
Moin Andi, also Anmeldeformular, da geht es darum, Angaben zu den Eltern,
Erziehungsberechtigten für die Rechnung und Kontakt, Vorname, Nachname,
Elternteil, dann Straße und Hausnummer, Postleitzahl und Ort, Telefonnummer,
E-Mail-Adresse und dann Angabe zum Kind, Name des Kindes, Geburtsdatum des
Kindes, Alter des Kindes, eventuell, also freiwillig, Diagnose. Dann
Einverständniserklärung: „Hiermit melde ich mein Kind verbindlich zum
Neuroscan Balance Intensive an. Ich bestätige, dass die angegebenen Daten
korrekt sind." Ort, Datum ist natürlich bei online dann wahrscheinlich nicht
notwendig und Unterschrift auch nicht. Das ist der-
```
