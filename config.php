<?php

// Configurazione percorsi file
define('USERS_FILE', __DIR__ . '/data/users.json');
define('PERSONE_FILE', __DIR__ . '/data/persone.json');

// Configurazione sessione
function inizializza_sessione() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Verifica autenticazione
function verifica_autenticazione() {
    inizializza_sessione();
    if (!isset($_SESSION['username'])) {
        header('Location: ' . get_base_path() . 'login/login.php');
        exit;
    }
}

// Ottieni il percorso base relativo
function get_base_path() {
    $current_path = $_SERVER['PHP_SELF'];
    $depth = substr_count(dirname($current_path), '/') - substr_count('/5ID-Rocchi/Progetto', '/');
    return str_repeat('../', max(0, $depth));
}

// Leggi file JSON e restituisci array
function leggi_json($file_path) {
    if (!file_exists($file_path)) {
        return [];
    }

    $json_data = file_get_contents($file_path);
    $data = json_decode($json_data, true);

    return ($data === null) ? [] : $data;
}

// Scrivi array in file JSON
function scrivi_json($file_path, $data) {
    $dir = dirname($file_path);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    return file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));
}

// Valida codice fiscale
function valida_codice_fiscale($codice_fiscale) {
    $pattern = '/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/';
    return preg_match($pattern, $codice_fiscale);
}

// Verifica se codice fiscale esiste già
function codice_fiscale_esiste($codice_fiscale) {
    $persone = leggi_json(PERSONE_FILE);
    foreach ($persone as $persona) {
        if ($persona['codice_fiscale'] === $codice_fiscale) {
            return true;
        }
    }
    return false;
}

// Verifica se username esiste già
function username_esiste($username) {
    $users = leggi_json(USERS_FILE);
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return true;
        }
    }
    return false;
}

// Verifica credenziali utente
function verifica_credenziali($username, $password) {
    $users = leggi_json(USERS_FILE);
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            return true;
        }
    }
    return false;
}

// Aggiungi nuovo utente
function aggiungi_utente($username, $password) {
    $users = leggi_json(USERS_FILE);
    $users[] = [
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ];
    return scrivi_json(USERS_FILE, $users);
}

// Aggiungi nuova persona
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

// Elimina persona per codice fiscale
function elimina_persona($codice_fiscale) {
    $persone = leggi_json(PERSONE_FILE);
    $persone_aggiornate = array_filter($persone, function($persona) use ($codice_fiscale) {
        return $persona['codice_fiscale'] !== $codice_fiscale;
    });

    // Reindex array
    $persone_aggiornate = array_values($persone_aggiornate);

    return scrivi_json(PERSONE_FILE, $persone_aggiornate);
}

// Ottieni tutte le persone con filtri opzionali
function ottieni_persone($filtro_cognome = '', $filtro_data_dopo = '') {
    $persone = leggi_json(PERSONE_FILE);

    if (empty($filtro_cognome) && empty($filtro_data_dopo)) {
        return $persone;
    }

    return array_filter($persone, function($persona) use ($filtro_cognome, $filtro_data_dopo) {
        $match = true;

        if (!empty($filtro_cognome)) {
            $match = $match && stripos($persona['cognome'], $filtro_cognome) !== false;
        }

        if (!empty($filtro_data_dopo)) {
            $match = $match && strtotime($persona['data_nascita']) > strtotime($filtro_data_dopo);
        }

        return $match;
    });
}

// Formatta data in formato italiano
function formatta_data($data) {
    return date('d/m/Y', strtotime($data));
}

?>

