<?php
/**
 * VORLAGE für die Admin-Login-Zugangsdaten (Termine/Preise/Zahlungsdaten pflegen).
 *
 * SO GEHT'S LIVE:
 *   1. Diese Datei auf dem Server (per FTP/WebFTP) kopieren zu:  admin/admin-config.php
 *      (also in denselben "admin"-Ordner, in dem auch index.php liegt)
 *   2. Benutzername/Passwort-Hash unten anpassen (Claude gibt dir fertige Werte).
 *   3. admin-config.php NICHT in Git einchecken (per .gitignore ausgeschlossen)
 *      und wird vom Deploy NICHT überschrieben/gelöscht.
 *
 * Passwort ändern: Neuen Hash erzeugen lassen (einfach fragen) oder selbst mit
 * PHP erzeugen:  php -r 'echo password_hash("neuesPasswort", PASSWORD_BCRYPT);'
 */

return [
    'username'      => 'willi',
    'password_hash' => '$2y$12$PLATZHALTER-WIRD-ERSETZT',
];
