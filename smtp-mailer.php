<?php
/**
 * Minimaler, abhängigkeitsfreier SMTP-Versand mit TLS – passend für All-Inkl.
 * Unterstützt implizites TLS (secure='ssl', Port 465) und STARTTLS (secure='tls', Port 587).
 *
 * Rückgabe: ['ok' => bool, 'error' => string]
 *
 * Kein Composer / keine externe Bibliothek nötig (statische Seite, kein Build-Schritt).
 */

function smtp_send(array $cfg, string $to, string $subject, string $body, string $replyToEmail = '', string $replyToName = '', string $htmlBody = ''): array {
    $host     = $cfg['host'] ?? '';
    $port     = (int)($cfg['port'] ?? 465);
    $secure   = $cfg['secure'] ?? 'ssl';               // 'ssl' (465) oder 'tls' (587/STARTTLS)
    $user     = $cfg['user'] ?? '';
    $pass     = $cfg['pass'] ?? '';
    $from     = $cfg['from'] ?? $user;
    $fromName = $cfg['from_name'] ?? 'NeuroScanBalance';
    $timeout  = 20;

    if ($host === '' || $user === '' || $pass === '') {
        return ['ok' => false, 'error' => 'SMTP-Konfiguration unvollständig'];
    }

    $remote = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'SNI_enabled' => true]]);
    $fp = @stream_socket_client($remote, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) {
        return ['ok' => false, 'error' => "Verbindung fehlgeschlagen: $errstr ($errno)"];
    }
    stream_set_timeout($fp, $timeout);

    $read = function () use ($fp) {
        $data = '';
        while (($line = fgets($fp, 515)) !== false) {
            $data .= $line;
            // Ende einer (ggf. mehrzeiligen) Antwort: 4. Zeichen ist ein Leerzeichen
            if (strlen($line) >= 4 && $line[3] === ' ') break;
        }
        return $data;
    };
    $code = function ($r) { return (int)substr($r, 0, 3); };
    $cmd  = function ($c) use ($fp, $read) { fwrite($fp, $c . "\r\n"); return $read(); };

    $fail = function ($msg) use ($fp) { @fclose($fp); return ['ok' => false, 'error' => $msg]; };

    if ($code($read()) !== 220) return $fail('Kein SMTP-Greeting (220)');

    $ehlo = 'EHLO neuroscanbalance-badessen.de';
    if ($code($cmd($ehlo)) !== 250) return $fail('EHLO abgelehnt');

    if ($secure === 'tls') {
        if ($code($cmd('STARTTLS')) !== 220) return $fail('STARTTLS abgelehnt');
        if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) return $fail('TLS-Handshake fehlgeschlagen');
        if ($code($cmd($ehlo)) !== 250) return $fail('EHLO nach STARTTLS abgelehnt');
    }

    if ($code($cmd('AUTH LOGIN')) !== 334) return $fail('AUTH LOGIN nicht angeboten');
    if ($code($cmd(base64_encode($user))) !== 334) return $fail('Benutzername abgelehnt');
    if ($code($cmd(base64_encode($pass))) !== 235) return $fail('Login fehlgeschlagen (Passwort/Ben.)');

    if ($code($cmd('MAIL FROM:<' . $from . '>')) !== 250) return $fail('MAIL FROM abgelehnt');
    $rcpt = $code($cmd('RCPT TO:<' . $to . '>'));
    if ($rcpt !== 250 && $rcpt !== 251) return $fail('Empfänger abgelehnt');
    if ($code($cmd('DATA')) !== 354) return $fail('DATA abgelehnt');

    // ---- Kopf + Text zusammenbauen ----
    $encSubj = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    $encName = '=?UTF-8?B?' . base64_encode($fromName) . '?=';
    $headers = [];
    $headers[] = 'Date: ' . date('r');
    $headers[] = 'Message-ID: <' . bin2hex(random_bytes(8)) . '@neuroscanbalance-badessen.de>';
    $headers[] = 'From: ' . $encName . ' <' . $from . '>';
    $headers[] = 'To: <' . $to . '>';
    if ($replyToEmail !== '') {
        $rn = $replyToName !== '' ? '=?UTF-8?B?' . base64_encode($replyToName) . '?= ' : '';
        $headers[] = 'Reply-To: ' . $rn . '<' . $replyToEmail . '>';
    }
    $headers[] = 'Subject: ' . $encSubj;
    $headers[] = 'MIME-Version: 1.0';

    if ($htmlBody !== '') {
        // Multipart: Text-Version (Fallback) + HTML-Version, damit jedes Mailprogramm
        // etwas Lesbares zeigt – moderne Clients (Outlook/Gmail/Apple Mail) rendern die HTML-Version.
        $boundary = 'nsb_' . bin2hex(random_bytes(12));
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
        $rawBody  = "This is a multi-part message in MIME format.\n";
        $rawBody .= "--{$boundary}\n";
        $rawBody .= "Content-Type: text/plain; charset=UTF-8\n";
        $rawBody .= "Content-Transfer-Encoding: 8bit\n\n";
        $rawBody .= $body . "\n\n";
        $rawBody .= "--{$boundary}\n";
        $rawBody .= "Content-Type: text/html; charset=UTF-8\n";
        $rawBody .= "Content-Transfer-Encoding: 8bit\n\n";
        $rawBody .= $htmlBody . "\n\n";
        $rawBody .= "--{$boundary}--";
    } else {
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $rawBody = $body;
    }

    // Body normalisieren auf CRLF + Dot-Stuffing (Zeilen, die mit "." beginnen)
    $rawBody = str_replace(["\r\n", "\r", "\n"], "\n", $rawBody);
    $lines = explode("\n", $rawBody);
    foreach ($lines as &$ln) { if (isset($ln[0]) && $ln[0] === '.') $ln = '.' . $ln; }
    unset($ln);

    $data = implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $lines) . "\r\n.";
    if ($code($cmd($data)) !== 250) return $fail('Nachricht abgelehnt');

    $cmd('QUIT');
    @fclose($fp);
    return ['ok' => true, 'error' => ''];
}
