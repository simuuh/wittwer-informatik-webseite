<?php
/**
 * Wittwer Informatik — Kontaktformular Handler
 * Wird per POST von index.php aufgerufen.
 * Gibt JSON zurück.
 */

header('Content-Type: application/json');

// Nur POST erlaubt
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Methode nicht erlaubt.']);
    exit;
}

$config = require __DIR__ . '/config.php';

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
$k   = $config['kontakt'];
$f   = $config['firma'];

$betreff = '=?UTF-8?B?' . base64_encode('Kontaktanfrage von ' . $name) . '?=';

$text = "Neue Kontaktanfrage über wittwer-informatik.ch\n\n";
$text .= "Name:    {$name}\n";
$text .= "E-Mail:  {$mail}\n";
$text .= "---\n\n";
$text .= $nachricht . "\n";

$headers  = "From: {$f['name']} <{$k['absender']}>\r\n";
$headers .= "Reply-To: {$name} <{$mail}>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

$gesendet = mail($k['empfaenger'], $betreff, $text, $headers);

if ($gesendet) {
    echo json_encode(['ok' => true]);
} else {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'E-Mail konnte nicht gesendet werden. Bitte direkt an hallo@wittwer-informatik.ch schreiben.']);
}
