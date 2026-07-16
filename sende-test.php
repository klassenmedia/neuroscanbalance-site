<?php
/**
 * TEMPORÄRES Test-Skript für den SMTP-Versand.
 * Aufruf im Browser:  https://neuroscanbalance-badessen.de/sende-test.php?run=nsbtest
 * Zeigt an, ob smtp-config.php gefunden wird und ob der Versand klappt.
 * NACH DEM TEST WIEDER LÖSCHEN (wird von Claude entfernt).
 */
header('Content-Type: text/plain; charset=UTF-8');

if (($_GET['run'] ?? '') !== 'nsbtest') {
    echo "Bitte mit ?run=nsbtest aufrufen.\n";
    exit;
}

$cfgfile = __DIR__ . '/smtp-config.php';
if (!is_file($cfgfile)) {
    echo "FEHLER: smtp-config.php wurde NICHT gefunden (liegt sie im richtigen Ordner, neben index.html?).\n";
    exit;
}
require __DIR__ . '/smtp-mailer.php';
$cfg = require $cfgfile;

echo "smtp-config.php gefunden.\n";
echo "Server: " . ($cfg['host'] ?? '?') . ":" . ($cfg['port'] ?? '?') . " (" . ($cfg['secure'] ?? '?') . ")\n";
echo "Absender: " . ($cfg['from'] ?? '?') . "\n\n";

$ziel = 'kontakt@klassen-media.de';
$text = "Dies ist eine Test-E-Mail vom NeuroScanBalance-Anmeldeformular.\n"
      . "Wenn du das liest, funktioniert der SMTP-Versand über All-Inkl.\n\n"
      . "Gesendet: " . date('d.m.Y H:i:s') . "\n";

$res = smtp_send($cfg, $ziel, 'NSB – Test-E-Mail (Anmeldeformular)', $text, $cfg['reply_to'] ?? ($cfg['from'] ?? ''), 'NeuroScanBalance');

if ($res['ok']) {
    echo "OK  ->  Test-E-Mail wurde an {$ziel} versendet. Bitte Postfach (und Spam-Ordner) prüfen.\n";
} else {
    echo "FEHLER beim Versand:\n  " . $res['error'] . "\n";
}
