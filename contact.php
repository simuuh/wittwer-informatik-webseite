<?php
/**
 * Wittwer Informatik — Kontaktformular Handler
 * Wird per POST von index.php aufgerufen.
 * Gibt JSON zurück.
 */

// Security-Header via PHP. OVHcloud Free Hosting hat kein mod_headers, darum hier.
// HSTS wird bewusst nicht via PHP gesetzt.
header_remove('X-Powered-By');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), camera=(), microphone=(), payment=(), usb=()');
header('X-XSS-Protection: 0');
// contact.php gibt nur JSON zurück, rendert nie HTML. Engste CSP genügt.
header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none';");

header('Content-Type: application/json');

// Nur POST erlaubt
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Methode nicht erlaubt.']);
    exit;
}

$config = require __DIR__ . '/config.php';

// Rate Limiting: max. 3 Anfragen pro IP in 10 Minuten
$ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ip_hash  = hash('sha256', $ip); // IP nicht im Klartext speichern
$rl_file  = sys_get_temp_dir() . '/wi_rl_' . $ip_hash;
$rl_limit = 3;
$rl_window = 600; // 10 Minuten in Sekunden

$rl_count = 0;
$rl_reset = time() + $rl_window;

if (file_exists($rl_file)) {
    $rl_data = json_decode(file_get_contents($rl_file), true);
    if ($rl_data && $rl_data['reset'] > time()) {
        $rl_count = $rl_data['count'];
        $rl_reset = $rl_data['reset'];
    }
}

if ($rl_count >= $rl_limit) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'fehler' => ['Zu viele Anfragen. Bitte warte 10 Minuten und versuche es erneut.']]);
    exit;
}

// Zähler erhöhen (wird nach erfolgreichem Send gespeichert)
$rl_count++;

// Eingaben bereinigen
$name     = trim(strip_tags($_POST['name'] ?? ''));
$mail     = trim(strip_tags($_POST['mail'] ?? ''));
$nachricht = trim(strip_tags($_POST['nachricht'] ?? ''));
$captcha  = $_POST['h-captcha-response'] ?? '';

// Validierung
$fehler = [];
if (mb_strlen($name) < 2)                    $fehler[] = 'Bitte gib deinen Namen ein.';
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) $fehler[] = 'Bitte gib eine gültige E-Mail-Adresse ein.';
if (mb_strlen($nachricht) < 10)               $fehler[] = 'Bitte schreib uns etwas (mindestens 10 Zeichen).';
if (empty($captcha))                          $fehler[] = 'Bitte bestätige das Captcha.';

if (!empty($fehler)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'fehler' => $fehler]);
    exit;
}

// hCaptcha verifizieren
$hc = $config['hcaptcha'];
if (!empty($hc['secret_key'])) {
    $verify = file_get_contents('https://api.hcaptcha.com/siteverify', false, stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query([
                'secret'   => $hc['secret_key'],
                'response' => $captcha,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ]),
        ],
    ]));
    $result = json_decode($verify, true);
    if (!($result['success'] ?? false)) {
        http_response_code(422);
        echo json_encode(['ok' => false, 'fehler' => ['Captcha ungültig. Bitte erneut versuchen.']]);
        exit;
    }
}

// Mail senden
$k = $config['kontakt'];
$f = $config['firma'];

if (!empty($k['ahasend_key'])) {
    // Mail senden via Ahasend SMTP API
    $payload = json_encode([
        'from' => [
            'name'    => $f['name'],
            'address' => $k['absender'],
        ],
        'to' => [[
            'address' => $k['empfaenger'],
        ]],
        'reply_to' => [[
            'address' => $mail,
        ]],
        'subject' => 'Kontaktanfrage von ' . $name,
        'text'    => "Neue Kontaktanfrage über wittwer-informatik.ch\n\nName:   {$name}\nE-Mail: {$mail}\n---\n\n{$nachricht}",
    ]);

    $ch = curl_init($k['ahasend_url']);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'X-Api-Key: ' . $k['ahasend_key'],
        ],
        CURLOPT_TIMEOUT        => 10,
    ]);

    $api_response = curl_exec($ch);
    $http_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $gesendet = ($http_code >= 200 && $http_code < 300);
} else {
    // Fallback: PHP mail()
    $headers  = "From: {$f['name']} <{$k['absender']}>\r\n";
    $headers .= "Reply-To: <{$mail}>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $betreff  = '=?UTF-8?B?' . base64_encode('Kontaktanfrage von ' . $name) . '?=';
    $text     = "Neue Kontaktanfrage über wittwer-informatik.ch\n\nName:   {$name}\nE-Mail: {$mail}\n---\n\n{$nachricht}";
    $gesendet = mail($k['empfaenger'], $betreff, $text, $headers);
}

if ($gesendet) {
    // Rate-Limiting-Zähler nur bei erfolgreichem Send speichern
    file_put_contents($rl_file, json_encode(['count' => $rl_count, 'reset' => $rl_reset]));
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'E-Mail konnte nicht gesendet werden. Bitte direkt an hallo@wittwer-informatik.ch schreiben.']);
}
