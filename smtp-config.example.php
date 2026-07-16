<?php
/**
 * VORLAGE für die SMTP-Konfiguration des Anmeldeformulars.
 *
 * SO GEHT'S LIVE:
 *   1. Diese Datei auf dem Server (per FTP) kopieren zu:  smtp-config.php
 *   2. Werte unten mit den echten All-Inkl-Zugangsdaten füllen.
 *   3. smtp-config.php NICHT in Git einchecken (ist per .gitignore ausgeschlossen)
 *      und wird vom Deploy NICHT überschrieben/gelöscht (steht in der exclude-Liste).
 *
 * Fehlt smtp-config.php, fällt anmeldung.php automatisch auf den einfachen
 * PHP-mail()-Versand zurück – das Formular funktioniert also auch ohne SMTP,
 * nur ohne die garantierte TLS-Verschlüsselung auf dem ersten Mail-Hop.
 *
 * Die SMTP-Zugangsdaten (Server/Port) stehen im All-Inkl-KAS beim Postfach
 * unter „E-Mail-Postfach" → Details. Meist:
 *   Server: w0XXXXXX.kasserver.com   Port 465 (SSL)  oder  587 (STARTTLS)
 */

return [
    'host'      => 'w0XXXXXX.kasserver.com',                 // All-Inkl SMTP-Server (aus dem KAS)
    'port'      => 465,                                       // 465 = SSL (empfohlen), 587 = STARTTLS
    'secure'    => 'ssl',                                     // 'ssl' für 465, 'tls' für 587
    'user'      => 'formular@neuroscanbalance-badessen.de',   // Postfach-Login
    'pass'      => 'HIER-DAS-POSTFACH-PASSWORT',              // Postfach-Passwort
    'from'      => 'formular@neuroscanbalance-badessen.de',   // Absender (muss = Postfach sein)
    'from_name' => 'NeuroScanBalance Anmeldung',

    // Wohin die Anmeldungen gehen (eine oder mehrere Adressen):
    'to'        => ['klassen@nsb-badessen.de'],

    // Reply-To für die Bestätigungs-Mail an die Eltern (Antworten landen bei Willi):
    'reply_to'  => 'klassen@nsb-badessen.de',

    // Automatische Eingangsbestätigung an die Eltern senden? true / false
    'confirm_to_parent' => true,
];
