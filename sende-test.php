<?php
/**
 * TEMPORÄRES Test-/Diagnose-Skript für den SMTP-Versand.
 * Aufruf:  https://neuroscanbalance-badessen.de/sende-test.php?run=nsbtest
 * NACH DEM TEST WIEDER LÖSCHEN (übernimmt Claude).
 */
header('Content-Type: text/plain; charset=UTF-8');

if (($_GET['run'] ?? '') !== 'nsbtest') {
    echo "Bitte mit ?run=nsbtest aufrufen.\n";
    exit;
}

echo "=== DIAGNOSE ===\n";
echo "Dieses Skript liegt im Ordner:\n  " . __DIR__ . "\n\n";
echo "Erwartete Konfig-Datei:\n  " . __DIR__ . "/smtp-config.php\n\n";
echo "Liegt index.html in diesem Ordner?  " . (is_file(__DIR__ . '/index.html') ? 'JA (richtiger Ordner)' : 'NEIN') . "\n\n";
echo "Gefundene Dateien mit 'smtp' im Namen (in genau diesem Ordner):\n";
$found = glob(__DIR__ . '/smtp*');
if ($found) { foreach ($found as $f) echo "  - " . basename($f) . "\n"; }
else { echo "  (keine)\n"; }
echo "\n";

// Beide Schreibweisen erlaubt (mit/ohne Bindestrich)
$cfgfile = __DIR__ . '/smtp-config.php';
if (!is_file($cfgfile) && is_file(__DIR__ . '/smtpconfig.php')) {
    $cfgfile = __DIR__ . '/smtpconfig.php';
}
if (!is_file($cfgfile)) {
    echo "ERGEBNIS: Keine Konfig-Datei gefunden (weder smtp-config.php noch smtpconfig.php).\n";
    echo "-> Bitte die Datei in den oben genannten Ordner legen.\n";
    exit;
}
echo "Verwendete Konfig-Datei: " . basename($cfgfile) . "\n\n";

require __DIR__ . '/smtp-mailer.php';
$cfg = require $cfgfile;

echo "smtp-config.php gefunden. ✔\n";
echo "Server:   " . ($cfg['host'] ?? '?') . ":" . ($cfg['port'] ?? '?') . " (" . ($cfg['secure'] ?? '?') . ")\n";
echo "Login:    " . ($cfg['user'] ?? '?') . "\n";
echo "Absender: " . ($cfg['from'] ?? '?') . "\n\n";

$ziel = 'kontakt@klassen-media.de';
$text = "Test-E-Mail vom NeuroScanBalance-Anmeldeformular.\n"
      . "Wenn du das liest, funktioniert der SMTP-Versand ueber All-Inkl.\n\n"
      . "Gesendet: " . date('d.m.Y H:i:s') . "\n";

$res = smtp_send($cfg, $ziel, 'NSB – Test-E-Mail (Anmeldeformular)', $text, $cfg['reply_to'] ?? ($cfg['from'] ?? ''), 'NeuroScanBalance');

echo $res['ok']
    ? "ERGEBNIS: OK – Test-E-Mail wurde an {$ziel} versendet. Bitte Postfach (auch Spam) pruefen.\n"
    : ("ERGEBNIS: FEHLER beim Versand:\n  " . $res['error'] . "\n");
