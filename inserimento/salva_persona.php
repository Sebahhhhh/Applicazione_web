<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $data_nascita = trim($_POST['data_nascita']);

    // Validazione base
    if (empty($codice_fiscale) || empty($nome) || empty($cognome) || empty($data_nascita)) {
        header('Location: aggiungi_persona.php?error=Tutti i campi sono obbligatori');
        exit;
    }

    // Validazione formato codice fiscale
    if (!preg_match('/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/', $codice_fiscale)) {
        header('Location: aggiungi_persona.php?error=Formato codice fiscale non valido');
        exit;
    }

    // Leggi il file JSON
    $file = '../data/persone.json';
    $persone = [];

    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        $persone = json_decode($json_data, true);

        if ($persone === null) {
            $persone = [];
        }
    }

    // Controllo duplicati
    foreach ($persone as $persona) {
        if ($persona['codice_fiscale'] === $codice_fiscale) {
            header('Location: aggiungi_persona.php?error=Codice fiscale già esistente');
            exit;
        }
    }

    // Aggiungi nuova persona
    $nuova_persona = [
        'codice_fiscale' => $codice_fiscale,
        'nome' => $nome,
        'cognome' => $cognome,
        'data_nascita' => $data_nascita
    ];

    $persone[] = $nuova_persona;

    // Salva nel file JSON
    if (file_put_contents($file, json_encode($persone, JSON_PRETTY_PRINT))) {
        header('Location: aggiungi_persona.php?success=1');
    } else {
        header('Location: aggiungi_persona.php?error=Errore nel salvataggio');
    }
    exit;
}

header('Location: ../dashboard.php');
exit;
