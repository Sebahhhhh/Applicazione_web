<?php
require_once '../config.php';
verifica_autenticazione();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $data_nascita = trim($_POST['data_nascita']);

    if (empty($codice_fiscale) || empty($nome) || empty($cognome) || empty($data_nascita)) {
        header('Location: aggiungi_persona.php?error=Tutti i campi sono obbligatori');
        exit;
    }

    if (!valida_codice_fiscale($codice_fiscale)) {
        header('Location: aggiungi_persona.php?error=Formato codice fiscale non valido');
        exit;
    }

    if (codice_fiscale_esiste($codice_fiscale)) {
        header('Location: aggiungi_persona.php?error=Codice fiscale già esistente');
        exit;
    }

    if (aggiungi_persona($codice_fiscale, $nome, $cognome, $data_nascita)) {
        header('Location: aggiungi_persona.php?success=1');
    } else {
        header('Location: aggiungi_persona.php?error=Errore nel salvataggio');
    }
    exit;
}

header('Location: ../dashboard.php');
exit;
