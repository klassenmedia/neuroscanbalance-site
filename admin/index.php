<?php
/**
 * Admin-Bereich: Willi pflegt hier selbst die Intensive-Termine (Ort/Datum),
 * die Preise und die Zahlungsdaten (IBAN) – ohne Code, ohne Git, ohne mich.
 *
 * Datenablage (bewusst NICHT in Git, siehe .gitignore + Deploy-exclude):
 *   - termine.json          (Domain-Root) – Termine + Preise, wird von index.html
 *                            und anmeldung.html per fetch() öffentlich gelesen.
 *   - zahlung-config.php    (Domain-Root) – IBAN etc., NUR serverseitig von
 *                            anmeldung.php gelesen, nie öffentlich abrufbar.
 *   - admin/admin-config.php – Login-Zugangsdaten (Benutzername + bcrypt-Hash).
 *
 * Warum eine eigene Datei statt der Startseite direkt bearbeiten? Jeder Push
 * auf GitHub lädt die Seite per Deploy neu hoch und würde von Hand gemachte
 * Änderungen sonst wieder überschreiben. Diese drei Dateien sind vom Deploy
 * ausgenommen – Admin-Pflege und Code-Updates kommen sich nie in die Quere.
 */

session_start();

$ROOT = dirname(__DIR__);
$TERMINE_DATEI = $ROOT . '/termine.json';
$ZAHLUNG_DATEI = $ROOT . '/zahlung-config.php';
$ADMIN_CFG_DATEI = __DIR__ . '/admin-config.php';

// ── CSRF-Schutz ──────────────────────────────────────────────
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(16)); }
$CSRF = $_SESSION['csrf'];
function csrf_ok() { return isset($_POST['csrf']) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$_POST['csrf']); }
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// ── Daten laden/speichern: Termine ───────────────────────────
function lade_termine($datei) {
    $default = [
        'preise' => ['einzel_lesson' => '80 €', 'intensive' => ''],
        'termine' => [],
    ];
    if (!is_file($datei)) return $default;
    $json = json_decode(file_get_contents($datei), true);
    if (!is_array($json)) return $default;
    $json += $default;
    $json['preise'] = (array)($json['preise'] ?? []) + $default['preise'];
    $json['termine'] = is_array($json['termine'] ?? null) ? $json['termine'] : [];
    return $json;
}
function speichere_termine($datei, array $data) {
    usort($data['termine'], function ($a, $b) { return strcmp($a['start'] ?? '', $b['start'] ?? ''); });
    $ok = @file_put_contents($datei, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), LOCK_EX);
    return $ok !== false;
}

// ── Daten laden/speichern: Zahlungsdaten ─────────────────────
function lade_zahlung($datei) {
    $default = ['kontoinhaber' => '', 'iban' => '', 'bic' => '', 'bank' => '', 'hinweis' => ''];
    if (!is_file($datei)) return $default;
    $data = require $datei;
    return is_array($data) ? ($data + $default) : $default;
}
function speichere_zahlung($datei, array $data) {
    $php = "<?php\n/**\n * Zahlungsdaten fuer die Anmelde-Bestaetigungsmail.\n"
         . " * Nicht in Git, nicht ueber's Web erreichbar (.htaccess) – nur admin/index.php und anmeldung.php lesen das.\n"
         . " */\nreturn " . var_export($data, true) . ";\n";
    return @file_put_contents($datei, $php, LOCK_EX) !== false;
}

// ── Admin-Login-Konfiguration laden ──────────────────────────
$admin_cfg = is_file($ADMIN_CFG_DATEI) ? (require $ADMIN_CFG_DATEI) : null;

// ── Logout ────────────────────────────────────────────────────
if (($_GET['logout'] ?? '') === '1') {
    $_SESSION = [];
    session_destroy();
    header('Location: index.php');
    exit;
}

$login_fehler = '';

// ── Login-Versuch ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    if (!csrf_ok()) {
        $login_fehler = 'Sicherheits-Token abgelaufen. Bitte Seite neu laden und erneut versuchen.';
    } elseif (!$admin_cfg) {
        $login_fehler = 'Keine Admin-Konfiguration gefunden.';
    } else {
        $u = trim($_POST['username'] ?? '');
        $p = (string)($_POST['password'] ?? '');
        if ($u === $admin_cfg['username'] && password_verify($p, $admin_cfg['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['nsb_admin'] = true;
            header('Location: index.php');
            exit;
        }
        $login_fehler = 'Benutzername oder Passwort ist falsch.';
    }
}

$eingeloggt = !empty($_SESSION['nsb_admin']);
$meldung = '';
$fehler = '';

// ── Aktionen (nur eingeloggt) ─────────────────────────────────
if ($eingeloggt && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (!csrf_ok()) {
        $fehler = 'Sicherheits-Token abgelaufen. Bitte erneut versuchen.';
    } elseif ($action === 'termin_hinzufuegen') {
        $ort = trim($_POST['ort'] ?? '');
        $start = trim($_POST['start'] ?? '');
        $end = trim($_POST['end'] ?? '');
        $sd = DateTime::createFromFormat('Y-m-d', $start);
        $ed = DateTime::createFromFormat('Y-m-d', $end);
        if ($ort === '' || !$sd || $sd->format('Y-m-d') !== $start || !$ed || $ed->format('Y-m-d') !== $end || $ed < $sd) {
            $fehler = 'Bitte Ort sowie ein gültiges Start- und Enddatum angeben (Ende darf nicht vor dem Start liegen).';
        } else {
            $daten = lade_termine($TERMINE_DATEI);
            $daten['termine'][] = ['id' => bin2hex(random_bytes(5)), 'ort' => $ort, 'start' => $start, 'end' => $end];
            speichere_termine($TERMINE_DATEI, $daten) ? $meldung = 'Termin hinzugefügt.' : $fehler = 'Speichern fehlgeschlagen (Schreibrechte prüfen).';
        }
    } elseif ($action === 'termin_speichern') {
        $id = $_POST['id'] ?? '';
        $ort = trim($_POST['ort'] ?? '');
        $start = trim($_POST['start'] ?? '');
        $end = trim($_POST['end'] ?? '');
        $sd = DateTime::createFromFormat('Y-m-d', $start);
        $ed = DateTime::createFromFormat('Y-m-d', $end);
        if ($ort === '' || !$sd || $sd->format('Y-m-d') !== $start || !$ed || $ed->format('Y-m-d') !== $end || $ed < $sd) {
            $fehler = 'Bitte Ort sowie ein gültiges Start- und Enddatum angeben.';
        } else {
            $daten = lade_termine($TERMINE_DATEI);
            $gefunden = false;
            foreach ($daten['termine'] as &$t) {
                if (($t['id'] ?? '') === $id) { $t['ort'] = $ort; $t['start'] = $start; $t['end'] = $end; $gefunden = true; break; }
            }
            unset($t);
            if ($gefunden) {
                speichere_termine($TERMINE_DATEI, $daten) ? $meldung = 'Termin gespeichert.' : $fehler = 'Speichern fehlgeschlagen.';
            } else {
                $fehler = 'Termin nicht gefunden.';
            }
        }
    } elseif ($action === 'termin_loeschen') {
        $id = $_POST['id'] ?? '';
        $daten = lade_termine($TERMINE_DATEI);
        $vorher = count($daten['termine']);
        $daten['termine'] = array_values(array_filter($daten['termine'], function ($t) use ($id) { return ($t['id'] ?? '') !== $id; }));
        if (count($daten['termine']) < $vorher) {
            speichere_termine($TERMINE_DATEI, $daten) ? $meldung = 'Termin gelöscht.' : $fehler = 'Speichern fehlgeschlagen.';
        } else {
            $fehler = 'Termin nicht gefunden.';
        }
    } elseif ($action === 'einstellungen_speichern') {
        $daten = lade_termine($TERMINE_DATEI);
        $daten['preise']['einzel_lesson'] = trim($_POST['einzel_lesson'] ?? '');
        $daten['preise']['intensive'] = trim($_POST['intensive_preis'] ?? '');
        speichere_termine($TERMINE_DATEI, $daten) ? $meldung = 'Preise gespeichert.' : $fehler = 'Speichern fehlgeschlagen.';

        $zahlung = [
            'kontoinhaber' => trim($_POST['kontoinhaber'] ?? ''),
            'iban'         => trim($_POST['iban'] ?? ''),
            'bic'          => trim($_POST['bic'] ?? ''),
            'bank'         => trim($_POST['bank'] ?? ''),
            'hinweis'      => trim($_POST['zahlung_hinweis'] ?? ''),
        ];
        speichere_zahlung($ZAHLUNG_DATEI, $zahlung) ? null : $fehler = trim(($fehler ?? '') . ' Zahlungsdaten konnten nicht gespeichert werden.');
    } elseif ($action === 'passwort_aendern') {
        $neu1 = (string)($_POST['neues_passwort'] ?? '');
        $neu2 = (string)($_POST['neues_passwort_wdh'] ?? '');
        if (strlen($neu1) < 8) {
            $fehler = 'Neues Passwort muss mindestens 8 Zeichen haben.';
        } elseif ($neu1 !== $neu2) {
            $fehler = 'Die beiden Passwörter stimmen nicht überein.';
        } else {
            $neuer_cfg = ['username' => $admin_cfg['username'] ?? 'willi', 'password_hash' => password_hash($neu1, PASSWORD_BCRYPT)];
            $php = "<?php\n/**\n * Admin-Login-Zugangsdaten. Nicht in Git, nicht ueber's Web erreichbar (.htaccess).\n */\nreturn "
                 . var_export($neuer_cfg, true) . ";\n";
            if (@file_put_contents($ADMIN_CFG_DATEI, $php, LOCK_EX) !== false) {
                $meldung = 'Passwort geändert.';
                $admin_cfg = $neuer_cfg;
            } else {
                $fehler = 'Passwort konnte nicht gespeichert werden (Schreibrechte prüfen).';
            }
        }
    }
}

$termine_daten = $eingeloggt ? lade_termine($TERMINE_DATEI) : ['preise' => ['einzel_lesson' => '', 'intensive' => ''], 'termine' => []];
$zahlung_daten = $eingeloggt ? lade_zahlung($ZAHLUNG_DATEI) : [];

function formatiere_datum_de($iso) {
    $d = DateTime::createFromFormat('Y-m-d', $iso);
    return $d ? $d->format('d.m.Y') : $iso;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin · NeuroScanBalance Bad Essen</title>
  <meta name="robots" content="noindex, nofollow">
  <meta name="theme-color" content="#0e6b8a">
  <link rel="icon" href="../favicon.ico" sizes="any">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&amp;display=swap">
  <link rel="stylesheet" href="../assets/css/style.v3.css">
  <style>
    .admin-wrap{max-width:820px;margin:0 auto;padding:2.4rem 1.5rem 4rem;}
    .admin-card{background:var(--bg-white);border-radius:18px;padding:1.8rem 1.8rem 2rem;box-shadow:0 16px 40px -18px rgba(14,107,138,.2);border:1px solid rgba(255,255,255,.8);margin-bottom:1.8rem;}
    .admin-card h2{font-size:1.2rem;font-weight:800;color:var(--navy);margin-bottom:1rem;}
    .admin-login{max-width:380px;margin:4rem auto;}
    .admin-termin-row{display:grid;grid-template-columns:1fr 150px 150px auto auto;gap:.6rem;align-items:end;padding:12px 0;border-bottom:1px solid #f0ece6;}
    .admin-termin-row:last-of-type{border-bottom:none;}
    .admin-termin-row .mini-field{display:flex;flex-direction:column;gap:.25rem;min-width:0;}
    .admin-termin-row .mini-field label{font-size:11px;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;}
    .admin-termin-row input[type=text],.admin-termin-row input[type=date]{width:100%;font-family:var(--sans);font-size:14px;padding:8px 9px;border:1px solid #d9d2c7;border-radius:8px;min-width:0;}
    .admin-row-vorbei{opacity:.45;}
    .admin-termin-header{display:grid;grid-template-columns:1fr 150px 150px auto auto;gap:.6rem;padding:0 0 6px;font-size:11px;font-weight:700;color:var(--text-light);text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #ece7df;margin-bottom:4px;}
    @media(max-width:680px){
      .admin-termin-row,.admin-termin-header{grid-template-columns:1fr;}
      .admin-termin-header{display:none;}
    }
    @media(max-width:480px){
      .admin-topbar{align-items:flex-start;}
      .admin-topbar h1{font-size:1.2rem;}
      .admin-logout{flex-shrink:0;padding-top:.3rem;}
    }
    .admin-btn{display:inline-flex;align-items:center;gap:5px;padding:7px 12px;border-radius:8px;font-size:13px;font-weight:700;border:none;cursor:pointer;font-family:var(--sans);white-space:nowrap;}
    .admin-btn-save{background:var(--navy);color:#fff;}
    .admin-btn-del{background:#fdecea;color:#b5372a;}
    .admin-btn-add{background:var(--accent);color:#fff;padding:10px 18px;}
    .admin-banner{border-radius:10px;padding:.8rem 1rem;margin-bottom:1.2rem;font-size:14px;}
    .admin-banner.ok{background:#e6f4ed;color:#1f7a4a;border:1px solid #bfe3cd;}
    .admin-banner.err{background:#fdecea;color:#b5372a;border:1px solid #f3c9c3;}
    .admin-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:1.6rem;}
    .admin-topbar h1{font-size:1.5rem;font-weight:800;color:var(--navy);}
    .admin-logout{font-size:13px;color:var(--text-muted);text-decoration:none;font-weight:600;}
    .admin-addform{display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:.7rem;align-items:end;margin-bottom:1.4rem;}
    .admin-addform label{display:block;font-size:12.5px;font-weight:600;color:var(--navy);margin-bottom:.3rem;}
    .admin-addform input{width:100%;font-family:var(--sans);font-size:14px;padding:9px 11px;border:1px solid #d9d2c7;border-radius:9px;}
    @media(max-width:680px){ .admin-addform{grid-template-columns:1fr;} .admin-table{display:block;overflow-x:auto;} }
  </style>
</head>
<body class="blog-body">

<?php if (!$admin_cfg): ?>

  <div class="admin-wrap admin-login">
    <div class="admin-card">
      <h2>Admin nicht eingerichtet</h2>
      <p style="font-size:14.5px;color:var(--text-muted);line-height:1.6;">Es wurde keine <code>admin/admin-config.php</code> gefunden. Bitte <code>admin/admin-config.example.php</code> zu <code>admin-config.php</code> kopieren und Zugangsdaten eintragen.</p>
    </div>
  </div>

<?php elseif (!$eingeloggt): ?>

  <div class="admin-wrap admin-login">
    <div class="admin-card">
      <h2>Admin-Login</h2>
      <?php if ($login_fehler): ?><div class="admin-banner err"><?= h($login_fehler) ?></div><?php endif; ?>
      <form method="post" class="form-grid" style="grid-template-columns:1fr;gap:1rem;">
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
        <div class="field">
          <label for="username">Benutzername</label>
          <input type="text" id="username" name="username" autocomplete="username" required autofocus>
        </div>
        <div class="field">
          <label for="password">Passwort</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        <button type="submit" class="form-submit" style="margin-top:.4rem;">Einloggen</button>
      </form>
    </div>
  </div>

<?php else: ?>

  <div class="admin-wrap">
    <div class="admin-topbar">
      <h1>NeuroScanBalance · Admin</h1>
      <a class="admin-logout" href="?logout=1">Abmelden</a>
    </div>

    <?php if ($meldung): ?><div class="admin-banner ok"><?= h($meldung) ?></div><?php endif; ?>
    <?php if ($fehler): ?><div class="admin-banner err"><?= h($fehler) ?></div><?php endif; ?>

    <div class="admin-card">
      <h2>Neuen Intensive-Termin hinzufügen</h2>
      <form method="post" class="admin-addform">
        <input type="hidden" name="action" value="termin_hinzufuegen">
        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
        <div><label>Ort</label><input type="text" name="ort" placeholder="z. B. Bad Essen" required></div>
        <div><label>Start</label><input type="date" name="start" required></div>
        <div><label>Ende</label><input type="date" name="end" required></div>
        <button type="submit" class="admin-btn admin-btn-add">+ Hinzufügen</button>
      </form>

      <?php if ($termine_daten['termine']): ?>
      <div class="admin-termin-header"><div>Ort</div><div>Start</div><div>Ende</div><div></div><div></div></div>
      <?php
      $heute = date('Y-m-d');
      foreach ($termine_daten['termine'] as $t):
        $vorbei = ($t['end'] ?? '') < $heute;
        $rid = h($t['id'] ?? '');
      ?>
      <div class="admin-termin-row <?= $vorbei ? 'admin-row-vorbei' : '' ?>">
        <!-- Leere Forms nur für die Übermittlung; Felder/Buttons hängen sich per form="…"
             von außen an (statt ungültig ineinander verschachtelt zu werden). -->
        <form method="post" id="tform-<?= $rid ?>" style="display:none;">
          <input type="hidden" name="action" value="termin_speichern">
          <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
          <input type="hidden" name="id" value="<?= $rid ?>">
        </form>
        <form method="post" id="dform-<?= $rid ?>" style="display:none;" onsubmit="return confirm('Diesen Termin wirklich löschen?');">
          <input type="hidden" name="action" value="termin_loeschen">
          <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
          <input type="hidden" name="id" value="<?= $rid ?>">
        </form>
        <div class="mini-field"><label>Ort</label><input type="text" name="ort" value="<?= h($t['ort'] ?? '') ?>" form="tform-<?= $rid ?>"></div>
        <div class="mini-field"><label>Start</label><input type="date" name="start" value="<?= h($t['start'] ?? '') ?>" form="tform-<?= $rid ?>"></div>
        <div class="mini-field"><label>Ende</label><input type="date" name="end" value="<?= h($t['end'] ?? '') ?>" form="tform-<?= $rid ?>"></div>
        <button type="submit" form="tform-<?= $rid ?>" class="admin-btn admin-btn-save">Speichern</button>
        <button type="submit" form="dform-<?= $rid ?>" class="admin-btn admin-btn-del">Löschen</button>
      </div>
      <?php endforeach; ?>
      <?php else: ?>
        <p style="color:var(--text-light);padding:10px 0;">Noch keine Termine angelegt.</p>
      <?php endif; ?>
      <p class="hint" style="margin-top:.8rem;">Vergangene Termine (grau) bleiben als Historie stehen und verschwinden automatisch von der anklickbaren Liste – du musst nichts löschen.</p>
    </div>

    <div class="admin-card">
      <h2>Preise</h2>
      <form method="post" class="form-grid">
        <input type="hidden" name="action" value="einstellungen_speichern">
        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
        <div class="field">
          <label for="einzel_lesson">Einzel-Lesson</label>
          <input type="text" id="einzel_lesson" name="einzel_lesson" value="<?= h($termine_daten['preise']['einzel_lesson'] ?? '') ?>" placeholder="z. B. 80 €">
        </div>
        <div class="field">
          <label for="intensive_preis">Intensive-Wochenende</label>
          <input type="text" id="intensive_preis" name="intensive_preis" value="<?= h($termine_daten['preise']['intensive'] ?? '') ?>" placeholder="leer lassen = „Preis auf Anfrage“">
        </div>
        <div class="field full">
          <label>Zahlungsdaten (erscheinen in der Bestätigungs-Mail an die Eltern)</label>
        </div>
        <div class="field full">
          <label for="kontoinhaber">Kontoinhaber</label>
          <input type="text" id="kontoinhaber" name="kontoinhaber" value="<?= h($zahlung_daten['kontoinhaber'] ?? '') ?>">
        </div>
        <div class="field">
          <label for="iban">IBAN</label>
          <input type="text" id="iban" name="iban" value="<?= h($zahlung_daten['iban'] ?? '') ?>" placeholder="DE...">
        </div>
        <div class="field">
          <label for="bic">BIC <span class="hint" style="font-weight:400;">(optional)</span></label>
          <input type="text" id="bic" name="bic" value="<?= h($zahlung_daten['bic'] ?? '') ?>">
        </div>
        <div class="field full">
          <label for="bank">Bank <span class="hint" style="font-weight:400;">(optional)</span></label>
          <input type="text" id="bank" name="bank" value="<?= h($zahlung_daten['bank'] ?? '') ?>">
        </div>
        <div class="field full">
          <label for="zahlung_hinweis">Hinweis zur Zahlung <span class="hint" style="font-weight:400;">(z. B. Verwendungszweck)</span></label>
          <textarea id="zahlung_hinweis" name="zahlung_hinweis" placeholder="Bitte den Namen des Kindes als Verwendungszweck angeben."><?= h($zahlung_daten['hinweis'] ?? '') ?></textarea>
        </div>
        <div class="field full">
          <button type="submit" class="form-submit" style="width:auto;">Speichern</button>
        </div>
      </form>
      <?php if (empty($termine_daten['preise']['intensive'])): ?>
        <p class="hint" style="margin-top:1rem;">Solange „Intensive-Wochenende" leer ist, zeigen Buchungsformular und Bestätigungs-Mail weiterhin „Preis auf Anfrage" – wie bisher auf der Startseite.</p>
      <?php endif; ?>
    </div>

    <div class="admin-card">
      <h2>Passwort ändern</h2>
      <form method="post" class="form-grid">
        <input type="hidden" name="action" value="passwort_aendern">
        <input type="hidden" name="csrf" value="<?= h($CSRF) ?>">
        <div class="field">
          <label for="neues_passwort">Neues Passwort</label>
          <input type="password" id="neues_passwort" name="neues_passwort" autocomplete="new-password" minlength="8">
        </div>
        <div class="field">
          <label for="neues_passwort_wdh">Wiederholen</label>
          <input type="password" id="neues_passwort_wdh" name="neues_passwort_wdh" autocomplete="new-password" minlength="8">
        </div>
        <div class="field full">
          <button type="submit" class="form-submit" style="width:auto;">Passwort ändern</button>
        </div>
      </form>
    </div>
  </div>

<?php endif; ?>

</body>
</html>
