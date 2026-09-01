<?php
/**
 * Wittwer Informatik — Zentrale Konfiguration
 * Hier alle dynamischen Werte anpassen.
 */
return [

    // Firmendaten
    'firma' => [
        'name'     => 'Wittwer Informatik',
        'inhaber'  => 'Simon Wittwer',
        'strasse'  => '',                          // TODO: Strasse und Hausnummer
        'plz'      => '',                          // TODO: PLZ
        'ort'      => '',                          // TODO: Ort
        'kanton'   => 'Kanton Bern',
        'land'     => 'Schweiz',
        'uid'      => 'CHE-348.095.495',
        'mail'     => 'hallo@wittwer-informatik.ch',
        'website'  => 'https://wittwer-informatik.ch',
    ],

    // hCaptcha
    // Keys erhältlich unter: https://dashboard.hcaptcha.com
    'hcaptcha' => [
        'site_key'   => '',                        // TODO: Site-Key eintragen
        'secret_key' => '',                        // TODO: Secret-Key eintragen (nicht im Git!)
    ],

    // Kontaktformular
    'kontakt' => [
        'empfaenger'  => 'hallo@wittwer-informatik.ch',
        'betreff'     => 'Neue Kontaktanfrage, Wittwer Informatik',
        'absender'    => 'noreply@wittwer-informatik.ch',
        'ahasend_key' => '',                       // TODO: API-Key aus Ahasend-Dashboard
        'ahasend_url' => 'https://api.ahasend.com/v1/email/send',
    ],

    // SEO
    'seo' => [
        'title'       => 'Wittwer Informatik | IT-Lösungen für KMU und Vereine',
        'description' => 'IT-Beratung und Softwareentwicklung für KMU und Vereine in der Schweiz. Microsoft 365, Open Source und Eigenentwicklungen. Kanton Bern.',
        'url'         => 'https://wittwer-informatik.ch',
    ],

];
