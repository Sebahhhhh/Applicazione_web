<?php

// configurazione percorsi file
define('USERS_FILE', __DIR__ . '/data/users.json');
define('PERSONE_FILE', __DIR__ . '/data/persone.json');


// verifica che la sessione sia avviata, utile per autenticazione e gestione utenti
function inizializza_sessione() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// verifica che l'utente sia autenticato, altrimenti lo reindirizza al login
function verifica_autenticazione() {
    inizializza_sessione();
    if (!isset($_SESSION['username'])) {
        header('Location: ' . get_base_path() . 'login/login.php');
        exit;
    }
}

// calcola il percorso base relativo per i redirect e gli include
function get_base_path() {
    $current_path = $_SERVER['PHP_SELF'];
    $depth = substr_count(dirname($current_path), '/') - substr_count('/5ID-Rocchi/Progetto', '/');
    return str_repeat('../', max(0, $depth));
}

// legge il file json e lo converte in array associativo
function leggi_json($file_path) {
    if (!file_exists($file_path)) {
        return [];
    }

    $json_data = file_get_contents($file_path);
    $data = json_decode($json_data, true);

    return ($data === null) ? [] : $data;
}

// salva l'array associativo nel file json, crea la cartella se non esiste
function scrivi_json($file_path, $data) {
    $dir = dirname($file_path);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    return file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));
}

// controlla che il codice fiscale abbia il formato corretto
function valida_codice_fiscale($codice_fiscale) {
    $pattern = '/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/';
    return preg_match($pattern, $codice_fiscale);
}

// verifica se il codice fiscale è già presente nel file delle persone
function codice_fiscale_esiste($codice_fiscale) {
    $persone = leggi_json(PERSONE_FILE);
    foreach ($persone as $persona) {
        if ($persona['codice_fiscale'] === $codice_fiscale) {
            return true;
        }
    }
    return false;
}

// verifica se lo username è già presente nel file degli utenti
function username_esiste($username) {
    $users = leggi_json(USERS_FILE);
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return true;
        }
    }
    return false;
}

// verifica che username e password corrispondano a un utente registrato
function verifica_credenziali($username, $password) {
    $users = leggi_json(USERS_FILE);
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            return true;
        }
    }
    return false;
}

// aggiunge un nuovo utente al file json degli utenti
function aggiungi_utente($username, $password) {
    $users = leggi_json(USERS_FILE);
    $users[] = [
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ];
    return scrivi_json(USERS_FILE, $users);
}

// aggiunge una nuova persona al file json delle persone
function aggiungi_persona($codice_fiscale, $nome, $cognome, $data_nascita) {
    $persone = leggi_json(PERSONE_FILE);
    $persone[] = [
        'codice_fiscale' => strtoupper($codice_fiscale),
        'nome' => $nome,
        'cognome' => $cognome,
        'data_nascita' => $data_nascita
    ];
    return scrivi_json(PERSONE_FILE, $persone);
}

// elimina una persona dal file json tramite codice fiscale
function elimina_persona($codice_fiscale) {
    $persone = leggi_json(PERSONE_FILE);
    $persone_aggiornate = array_filter($persone, function($persona) use ($codice_fiscale) {
        return $persona['codice_fiscale'] !== $codice_fiscale;
    });

    // reindicizza l'array dopo la rimozione
    $persone_aggiornate = array_values($persone_aggiornate);

    return scrivi_json(PERSONE_FILE, $persone_aggiornate);
}

// filtra le persone per cognome e/o data di nascita
function ottieni_persone($filtro_cognome = '', $filtro_data_dopo = '') {
    $persone = leggi_json(PERSONE_FILE);

    if (empty($filtro_cognome) && empty($filtro_data_dopo)) {
        return $persone;
    }

    return array_filter($persone, function($persona) use ($filtro_cognome, $filtro_data_dopo) {
        $match = true;

        // filtra per cognome se richiesto
        if (!empty($filtro_cognome)) {
            $match = $match && stripos($persona['cognome'], $filtro_cognome) !== false;
        }

        // filtra per data di nascita se richiesto
        if (!empty($filtro_data_dopo)) {
            $match = $match && strtotime($persona['data_nascita']) > strtotime($filtro_data_dopo);
        }

        return $match;
    });
}

// formatta la data in formato italiano gg/mm/aaaa
function formatta_data($data) {
    return date('d/m/Y', strtotime($data));
}

?>
