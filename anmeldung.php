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

// Foto-/Videoeinwilligung: eine der gültigen Optionen muss gewählt sein
$foto_optionen = ['Keine Aufnahmen', 'Nur ohne erkennbares Gesicht', 'Auch mit erkennbarem Gesicht'];
$foto = clean($_POST['foto_einwilligung'] ?? '');
if (!in_array($foto, $foto_optionen, true)) {
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
$text .= "  [x] Datenschutz / Verarbeitung eingewilligt\n";
$text .= "  Foto/Video: {$foto}\n\n";
$text .= "----------------------------------------\n";
$text .= "Gesendet am " . date('d.m.Y \u\m H:i') . " Uhr über neuroscanbalance-badessen.de\n";

// Optionale Eingangsbestätigung an die Eltern
$eltern_name = trim($vorname . ' ' . $nachname);
$confirm_betreff = 'Deine Anmeldung zum NeuroScanBalance-Intensive';
$confirm_text  = "Hallo {$eltern_name},\n\n";
$confirm_text .= "danke für deine Anmeldung – wir haben sie erhalten und melden uns\n";
$confirm_text .= "persönlich bei dir, um alles Weitere in Ruhe zu besprechen.\n\n";
$confirm_text .= "Deine Angaben zur Sicherheit:\n";
$confirm_text .= "  Intensive: {$intensive}\n";
$confirm_text .= "  Kind:      {$kind_name}\n\n";
$confirm_text .= "Wenn etwas nicht stimmt, antworte einfach auf diese E-Mail.\n\n";
$confirm_text .= "Herzliche Grüße\nWilli Klassen\nNeuroScanBalance Bad Essen\n";

// ─────────────────────────────────────────────────────────────
// Versand: bevorzugt SMTP (TLS) über smtp-config.php, sonst PHP mail()
// WICHTIG zur Zustellbarkeit: Absender ist bewusst eine Adresse der WEBSITE-Domain
// (neuroscanbalance-badessen.de, liegt bei All-Inkl) – NICHT die private Adresse
// @nsb-badessen.de. So passt der SPF-Eintrag, egal wo das Postfach @nsb-badessen.de
// gehostet ist (z. B. Microsoft). Antworten von Willi laufen per Reply-To.
// ─────────────────────────────────────────────────────────────
// Konfig-Datei suchen – beide Schreibweisen erlaubt (mit/ohne Bindestrich)
$cfgfile = __DIR__ . '/smtp-config.php';
if (!is_file($cfgfile) && is_file(__DIR__ . '/smtpconfig.php')) {
    $cfgfile = __DIR__ . '/smtpconfig.php';
}

// Server-Logfile (nur Ergebnisse/Fehler – KEINE personenbezogenen Daten der Familie).
// Liegt geschützt (.htaccess verbietet Web-Zugriff) und wird vom Deploy nicht angefasst.
$logfile = __DIR__ . '/formular-log.txt';
$log = function ($msg) use ($logfile) {
    @file_put_contents($logfile, date('Y-m-d H:i:s') . '  ' . $msg . "\n", FILE_APPEND | LOCK_EX);
};

$ok = true;

if (is_file($cfgfile)) {
    $cfg = require $cfgfile;
    require_once __DIR__ . '/smtp-mailer.php';
    $log('Neue Anmeldung – Versand per SMTP (' . ($cfg['host'] ?? '?') . ')');

    // 1) Benachrichtigung an Willi (Reply-To = Eltern → 1 Klick antwortet der Familie)
    foreach (($cfg['to'] ?? $EMPFAENGER) as $to) {
        $res = smtp_send($cfg, $to, $betreff, $text, $email, $eltern_name);
        $log('  -> Benachrichtigung an ' . $to . ': ' . ($res['ok'] ? 'OK' : 'FEHLER: ' . $res['error']));
        if (!$res['ok']) { $ok = false; }
    }

    // 2) Optionale Bestätigung an die Eltern (Reply-To = Willi)
    if (!empty($cfg['confirm_to_parent'])) {
        $replyWilli = $cfg['reply_to'] ?? ($cfg['to'][0] ?? '');
        // Bestätigung ist "nice to have" – ein Fehler hier soll die Anmeldung nicht scheitern lassen
        $rc = smtp_send($cfg, $email, $confirm_betreff, $confirm_text, $replyWilli, 'Willi Klassen');
        $log('  -> Bestaetigung an Eltern: ' . ($rc['ok'] ? 'OK' : 'FEHLER: ' . $rc['error']));
    }
} else {
    // Fallback ohne SMTP: einfacher PHP-mail()-Versand
    $log('Neue Anmeldung – smtp-config.php NICHT gefunden (' . $cfgfile . ') -> Fallback PHP mail()');
    $headers  = "From: NSB Anmeldung <{$ABSENDER}>\r\n";
    $headers .= "Reply-To: {$eltern_name} <{$email}>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $betreff_enc = '=?UTF-8?B?' . base64_encode($betreff) . '?=';
    foreach ($EMPFAENGER as $to) {
        $sent = mail($to, $betreff_enc, $text, $headers, '-f ' . $ABSENDER);
        $log('  -> mail() an ' . $to . ': ' . ($sent ? 'OK (an Mailserver uebergeben)' : 'FEHLER'));
        if (!$sent) { $ok = false; }
    }
}

if ($ok) {
    header('Location: danke.html');
    exit;
}
zurueck_mit_fehler();
