<?php
/**
 * Verarbeitet die Intensive-Anmeldung und schickt sie per E-Mail.
 * Läuft auf All-Inkl (PHP). Statisches Formular -> POST hierher.
 *
 * ┌────────────────────────────────────────────────────────────┐
 * │  EMPFÄNGER HIER EINTRAGEN (final noch zu bestätigen):       │
 * └────────────────────────────────────────────────────────────┘
 */
$EMPFAENGER = ['klassen@nsb-badessen.de'];              // <-- hier Adresse(n) setzen; Tanja ggf. ergänzen: 'tanja@neuroscanbalance-owl.de'
$ABSENDER   = 'no-reply@neuroscanbalance-badessen.de';   // Muss eine Adresse der eigenen Domain sein (All-Inkl-Vorgabe)
// ────────────────────────────────────────────────────────────

// Nur POST zulassen
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: anmeldung.html');
    exit;
}

// Zurück zum Formular mit Fehlerhinweis
function zurueck_mit_fehler() {
    header('Location: anmeldung.html?fehler=1');
    exit;
}

// Header-Injection verhindern (Zeilenumbrüche raus)
function clean($v) {
    $v = is_string($v) ? trim($v) : '';
    return str_replace(["\r", "\n", "%0a", "%0d"], ' ', $v);
}

// Spamschutz: Honeypot muss leer sein
if (!empty($_POST['website'])) {
    header('Location: danke.html'); // Bots ins Leere laufen lassen, keine Mail
    exit;
}

// Pflichtfelder
$pflicht = ['intensive','eltern_vorname','eltern_nachname','strasse','plz','ort','telefon','email','kind_name','kind_geburtsdatum'];
foreach ($pflicht as $f) {
    if (empty($_POST[$f]) || trim($_POST[$f]) === '') {
        zurueck_mit_fehler();
    }
}

// Einwilligungen müssen gesetzt sein
if (($_POST['einverstaendnis'] ?? '') !== 'ja' || ($_POST['datenschutz'] ?? '') !== 'ja') {
    zurueck_mit_fehler();
}

// E-Mail plausibel?
$email = clean($_POST['email']);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    zurueck_mit_fehler();
}

// Felder einsammeln
$intensive   = clean($_POST['intensive']);
$vorname     = clean($_POST['eltern_vorname']);
$nachname    = clean($_POST['eltern_nachname']);
$elternteil  = clean($_POST['elternteil'] ?? '');
$strasse     = clean($_POST['strasse']);
$plz         = clean($_POST['plz']);
$ort         = clean($_POST['ort']);
$telefon     = clean($_POST['telefon']);
$kind_name   = clean($_POST['kind_name']);
$kind_geb    = clean($_POST['kind_geburtsdatum']);
$kind_alter  = clean($_POST['kind_alter'] ?? '');
$diagnose    = trim($_POST['diagnose'] ?? '');
$diagnose    = str_replace(["\r\n", "\r"], "\n", $diagnose); // Zeilenumbrüche im Freitext erlaubt

// E-Mail-Text
$betreff = "Neue Intensive-Anmeldung: {$kind_name} ({$intensive})";
$text  = "Neue Anmeldung über das Online-Formular\n";
$text .= "========================================\n\n";
$text .= "GEWÜNSCHTES INTENSIVE\n  {$intensive}\n\n";
$text .= "ELTERN / ERZIEHUNGSBERECHTIGTE\n";
$text .= "  Name:      {$vorname} {$nachname}\n";
if ($elternteil !== '') { $text .= "  Elternteil: {$elternteil}\n"; }
$text .= "  Adresse:   {$strasse}, {$plz} {$ort}\n";
$text .= "  Telefon:   {$telefon}\n";
$text .= "  E-Mail:    {$email}\n\n";
$text .= "KIND\n";
$text .= "  Name:        {$kind_name}\n";
$text .= "  Geburtsdatum: {$kind_geb}\n";
if ($kind_alter !== '') { $text .= "  Alter:       {$kind_alter}\n"; }
$text .= "  Diagnose:    " . ($diagnose !== '' ? $diagnose : '(keine Angabe)') . "\n\n";
$text .= "EINWILLIGUNGEN\n";
$text .= "  [x] Verbindliche Anmeldung bestätigt\n";
$text .= "  [x] Datenschutz / Verarbeitung eingewilligt\n\n";
$text .= "----------------------------------------\n";
$text .= "Gesendet am " . date('d.m.Y \u\m H:i') . " Uhr über neuroscanbalance-badessen.de\n";

// Header
$headers  = "From: NSB Anmeldung <{$ABSENDER}>\r\n";
$headers .= "Reply-To: {$vorname} {$nachname} <{$email}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// Senden (an alle Empfänger)
// WICHTIG zur Zustellbarkeit: Absender (From) und Envelope-Sender (-f) sind bewusst
// eine Adresse der WEBSITE-Domain (neuroscanbalance-badessen.de, liegt bei All-Inkl) –
// NICHT die private Adresse @nsb-badessen.de. So passt der SPF-Eintrag, egal wo das
// Postfach @nsb-badessen.de gehostet ist (z. B. Microsoft). Der Empfänger (To) darf
// jede beliebige Adresse sein. Antworten gehen per Reply-To an die Eltern.
$betreff_enc = '=?UTF-8?B?' . base64_encode($betreff) . '?=';
$ok = true;
foreach ($EMPFAENGER as $to) {
    if (!mail($to, $betreff_enc, $text, $headers, '-f ' . $ABSENDER)) {
        $ok = false;
    }
}

if ($ok) {
    header('Location: danke.html');
    exit;
}
zurueck_mit_fehler();
