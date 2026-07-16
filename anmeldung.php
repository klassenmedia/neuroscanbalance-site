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

// Geburtsdatum gültig und nicht in der Zukunft?
$kind_geb_raw = clean($_POST['kind_geburtsdatum']);
$geb_dt = DateTime::createFromFormat('Y-m-d', $kind_geb_raw);
$heute  = new DateTime('today');
if (!$geb_dt || $geb_dt->format('Y-m-d') !== $kind_geb_raw || $geb_dt > $heute) {
    zurueck_mit_fehler();
}

// Alter serverseitig berechnen (nicht dem Browser vertrauen) – Jahre, sonst Monate, sonst Tage
function berechne_alter(DateTime $geb, DateTime $heute) {
    $diff = $geb->diff($heute);
    if ($diff->y >= 1) return $diff->y . ($diff->y === 1 ? ' Jahr' : ' Jahre');
    if ($diff->m >= 1) return $diff->m . ($diff->m === 1 ? ' Monat' : ' Monate');
    return $diff->d . ($diff->d === 1 ? ' Tag' : ' Tage');
}
$kind_alter = berechne_alter($geb_dt, $heute);

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
$text .= "  Geburtsdatum: " . $geb_dt->format('d.m.Y') . "\n";
$text .= "  Alter:       {$kind_alter}\n";
$text .= "  Diagnose:    " . ($diagnose !== '' ? $diagnose : '(keine Angabe)') . "\n\n";
$text .= "EINWILLIGUNGEN\n";
$text .= "  [x] Verbindliche Anmeldung bestätigt\n";
$text .= "  [x] Datenschutz / Verarbeitung eingewilligt\n";
$text .= "  Foto/Video: {$foto}\n\n";
$text .= "----------------------------------------\n";
$text .= "Gesendet am " . date('d.m.Y \u\m H:i') . " Uhr über neuroscanbalance-badessen.de\n";

// ─────────────────────────────────────────────────────────────
// HTML-Versionen (fett/strukturiert) – Klartext oben bleibt als Fallback erhalten
// ─────────────────────────────────────────────────────────────
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function email_zeile($label, $wert, $stark = true) {
    $stil = $stark ? 'font-weight:700;' : '';
    return '<tr>'
      . '<td style="padding:5px 14px 5px 0;color:#8b8b8b;font-size:13px;white-space:nowrap;vertical-align:top;">' . h($label) . '</td>'
      . '<td style="padding:5px 0;font-size:15px;color:#1a1a1a;' . $stil . '">' . $wert . '</td>'
      . '</tr>';
}

function email_abschnitt($titel) {
    return '<tr><td colspan="2" style="padding:20px 0 8px;border-top:1px solid #eee7db;font-size:13px;font-weight:800;color:#0e6b8a;text-transform:uppercase;letter-spacing:.05em;">' . h($titel) . '</td></tr>';
}

function email_huelle($kopfFarbe, $kopfLabel, $kopfTitel, $innenHtml) {
    return '<div style="background:#f7f4ef;padding:24px 12px;font-family:Arial,Helvetica,sans-serif;">'
      . '<div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #eee7db;">'
      . '<div style="background:' . $kopfFarbe . ';padding:22px 26px;">'
      . '<p style="margin:0 0 4px;color:rgba(255,255,255,.8);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;">' . h($kopfLabel) . '</p>'
      . '<p style="margin:0;color:#ffffff;font-size:20px;font-weight:800;">' . h($kopfTitel) . '</p>'
      . '</div>'
      . '<div style="padding:22px 26px 26px;">' . $innenHtml . '</div>'
      . '</div>'
      . '<p style="max-width:600px;margin:14px auto 0;text-align:center;font-size:11.5px;color:#aaa;">NeuroScanBalance Bad Essen · neuroscanbalance-badessen.de</p>'
      . '</div>';
}

// -- HTML für Willi: strukturierte Tabelle, sofort scannbar --
$diagnose_html = $diagnose !== '' ? nl2br(h($diagnose)) : '<span style="color:#999;">(keine Angabe)</span>';
$willi_tabelle  = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">';
$willi_tabelle .= email_abschnitt('Gewünschtes Intensive');
$willi_tabelle .= email_zeile('Termin', h($intensive));
$willi_tabelle .= email_abschnitt('Eltern / Erziehungsberechtigte');
$willi_tabelle .= email_zeile('Name', h($vorname . ' ' . $nachname));
if ($elternteil !== '') { $willi_tabelle .= email_zeile('Elternteil', h($elternteil), false); }
$willi_tabelle .= email_zeile('Adresse', h("{$strasse}, {$plz} {$ort}"), false);
$willi_tabelle .= email_zeile('Telefon', '<a href="tel:' . h($telefon) . '" style="color:#1a1a1a;font-weight:700;text-decoration:none;">' . h($telefon) . '</a>');
$willi_tabelle .= email_zeile('E-Mail', '<a href="mailto:' . h($email) . '" style="color:#0e6b8a;font-weight:700;text-decoration:none;">' . h($email) . '</a>');
$willi_tabelle .= email_abschnitt('Kind');
$willi_tabelle .= email_zeile('Name', h($kind_name));
$willi_tabelle .= email_zeile('Geburtsdatum', h($geb_dt->format('d.m.Y')) . ' <span style="font-weight:400;color:#8b8b8b;">(' . h($kind_alter) . ')</span>');
$willi_tabelle .= email_zeile('Diagnose', $diagnose_html, false);
$willi_tabelle .= email_abschnitt('Einwilligungen');
$willi_tabelle .= email_zeile('Anmeldung', '✅ verbindlich bestätigt', false);
$willi_tabelle .= email_zeile('Datenschutz', '✅ eingewilligt', false);
$foto_farbe = $foto === 'Keine Aufnahmen' ? '#b5372a' : '#1f7a4a';
$willi_tabelle .= email_zeile('Foto/Video', '<span style="color:' . $foto_farbe . ';font-weight:700;">' . h($foto) . '</span>', false);
$willi_tabelle .= '</table>';
$willi_tabelle .= '<p style="margin:22px 0 0;padding-top:14px;border-top:1px solid #eee7db;font-size:12px;color:#aaa;">Gesendet am ' . date('d.m.Y \u\m H:i') . ' Uhr über das Online-Formular. Antworten gehen direkt an ' . h($email) . '.</p>';
$html = email_huelle('#0e6b8a', 'NeuroScanBalance · Online-Anmeldung', '🔔 Neue Intensive-Anmeldung', $willi_tabelle);

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

// -- HTML für die Eltern-Bestätigung: freundlich, mit hervorgehobener Info-Box --
$confirm_inhalt  = '<p style="margin:0 0 16px;font-size:15.5px;line-height:1.6;color:#1a1a1a;">Hallo <strong>' . h($eltern_name) . '</strong>,</p>';
$confirm_inhalt .= '<p style="margin:0 0 18px;font-size:15.5px;line-height:1.6;color:#1a1a1a;">danke für deine Anmeldung – wir haben sie erhalten und melden uns <strong>persönlich</strong> bei dir, um alles Weitere in Ruhe zu besprechen.</p>';
$confirm_inhalt .= '<div style="background:#f0ece6;border-radius:10px;padding:16px 18px;margin-bottom:18px;">';
$confirm_inhalt .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0">';
$confirm_inhalt .= email_zeile('Intensive', h($intensive));
$confirm_inhalt .= email_zeile('Kind', h($kind_name));
$confirm_inhalt .= '</table></div>';
$confirm_inhalt .= '<p style="margin:0;font-size:14.5px;line-height:1.6;color:#5a5a5a;">Wenn etwas nicht stimmt, antworte einfach auf diese E-Mail.</p>';
$confirm_inhalt .= '<p style="margin:22px 0 0;font-size:15px;line-height:1.6;color:#1a1a1a;">Herzliche Grüße<br><strong>Willi Klassen</strong><br>NeuroScanBalance Bad Essen</p>';
// Einfarbig (kein CSS-Gradient) – Farbverlaeufe werden in vielen Mailprogrammen nicht unterstuetzt
$confirm_html = email_huelle('#0e6b8a', 'NeuroScanBalance Bad Essen', '✅ Anmeldung bestätigt', $confirm_inhalt);

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
        $res = smtp_send($cfg, $to, $betreff, $text, $email, $eltern_name, $html);
        $log('  -> Benachrichtigung an ' . $to . ': ' . ($res['ok'] ? 'OK' : 'FEHLER: ' . $res['error']));
        if (!$res['ok']) { $ok = false; }
    }

    // 2) Optionale Bestätigung an die Eltern (Reply-To = Willi)
    if (!empty($cfg['confirm_to_parent'])) {
        $replyWilli = $cfg['reply_to'] ?? ($cfg['to'][0] ?? '');
        // Bestätigung ist "nice to have" – ein Fehler hier soll die Anmeldung nicht scheitern lassen
        $rc = smtp_send($cfg, $email, $confirm_betreff, $confirm_text, $replyWilli, 'Willi Klassen', $confirm_html);
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
