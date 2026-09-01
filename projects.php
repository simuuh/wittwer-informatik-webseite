<?php
/**
 * Wittwer Informatik — Projektliste
 * Jedes Projekt erscheint als Karte auf der Startseite.
 * Beim Klick öffnet sich ein Modal mit weiteren Infos und Bildern.
 *
 * Bilder: unter /assets/projects/{slug}/ ablegen.
 * Empfohlene Grösse: 1200x750px, JPG 80%.
 * Platzhalter werden automatisch angezeigt solange keine Bilder vorhanden sind.
 */
return [

    [
        'slug'        => 'versammlungshub',
        'name'        => 'VersammlungsHub',
        'tag'         => 'SaaS',
        'status'      => 'Live',
        'kurz'        => 'Versammlungen digital verwalten. Für Vereine, Stockwerkeigentümergemeinschaften und Genossenschaften in der Schweiz.',
        'beschreibung' => 'VersammlungsHub vereinfacht die Verwaltung von Versammlungen für Schweizer Organisationen. Digitale Abstimmungen, Protokollführung, Finanzverwaltung und Mitgliederverwaltung in einer Plattform. Datenschutz nach Schweizer Recht (nDSG), gehostet in der Schweiz und der EU.',
        'features'    => [
            'Digitale Abstimmungen und Zirkularbeschlüsse',
            'Protokollführung mit PDF-Export',
            'Mitgliederverwaltung und Einladungsversand',
            'Finanzverwaltung mit Belegverwaltung',
            'Freemium-Modell, Schweizer Recht',
        ],
        'stack'       => ['Flask', 'PostgreSQL', 'Keycloak', 'Docker'],
        'url'         => 'https://versammlungshub.ch',
        'url_label'   => 'versammlungshub.ch',
        'bilder'      => [
            // 'assets/projects/versammlungshub/hero.jpg',
            // 'assets/projects/versammlungshub/abstimmung.jpg',
            // 'assets/projects/versammlungshub/protokoll.jpg',
        ],
    ],

    [
        'slug'        => 'mitfahrli',
        'name'        => 'Mitfahrli',
        'tag'         => 'App',
        'status'      => 'Live',
        'kurz'        => 'Fahrgemeinschaften für Vereine und Teams. Kein Login, kein Tracking, einfach ein Link.',
        'beschreibung' => 'Mitfahrli löst ein konkretes Problem: Wer fährt mit wem zum Auswärtsspiel, zum Vereinsanlass oder zur Schulreise? Ohne App-Download, ohne Anmeldung. Einfach einen Event-Code erstellen, Link teilen, fertig. Alles läuft über Magic Links.',
        'features'    => [
            'Kein Login, kein Account',
            'Fahrer und Mitfahrer per Magic Link',
            'Event-Code zum Teilen',
            'Kein Tracking, keine Werbung',
            'Funktioniert auf jedem Gerät',
        ],
        'stack'       => ['FastAPI', 'PostgreSQL', 'Docker'],
        'url'         => 'https://mitfahrli.ch',
        'url_label'   => 'mitfahrli.ch',
        'bilder'      => [
            // 'assets/projects/mitfahrli/hero.jpg',
            // 'assets/projects/mitfahrli/event.jpg',
        ],
    ],

    [
        'slug'        => 'platzhalter',
        'name'        => 'Nächstes Projekt',
        'tag'         => '',
        'status'      => 'Kommt bald',
        'kurz'        => 'Weiteres Projekt in Planung.',
        'beschreibung' => '',
        'features'    => [],
        'stack'       => [],
        'url'         => '',
        'url_label'   => '',
        'bilder'      => [],
        'placeholder' => true,
    ],

];
