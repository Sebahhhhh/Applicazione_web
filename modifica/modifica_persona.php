<?php
require_once '../config.php';
// verifica che l'utente sia autenticato prima di mostrare la pagina
verifica_autenticazione();

// inizializza variabili per messaggio ed esito
$messaggio = '';
$persona_trovata = null;

// gestisce la ricerca della persona da modificare
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cerca'])) {
    // prende il codice fiscale dal form e lo normalizza
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));
    // carica tutte le persone dal file json
    $persone = leggi_json(PERSONE_FILE);
    foreach ($persone as $p) {
        if ($p['codice_fiscale'] === $codice_fiscale) {
            $persona_trovata = $p;
            break;
        }
    }
    // mostra errore se la persona non viene trovata
    if (!$persona_trovata) {
        $messaggio = "Nessuna persona trovata con il codice fiscale: $codice_fiscale";
    }
}

// gestisce la modifica della persona
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifica'])) {
    // prende e normalizza i dati dal form
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $data_nascita = trim($_POST['data_nascita']);

    // carica tutte le persone dal file json
    $persone = leggi_json(PERSONE_FILE);
    $trovato = false;
    foreach ($persone as &$p) {
        if ($p['codice_fiscale'] === $codice_fiscale) {
            // aggiorna i dati della persona trovata
            $p['nome'] = $nome;
            $p['cognome'] = $cognome;
            $p['data_nascita'] = $data_nascita;
            $trovato = true;
            break;
        }
    }
    // salva i dati modificati nel file json
    if ($trovato) {
        scrivi_json(PERSONE_FILE, $persone);
        $messaggio = "Modifica avvenuta con successo!";
        $persona_trovata = [
            'codice_fiscale' => $codice_fiscale,
            'nome' => $nome,
            'cognome' => $cognome,
            'data_nascita' => $data_nascita
        ];
    } else {
        // mostra errore se la persona non viene trovata
        $messaggio = "Nessuna persona trovata con il codice fiscale: $codice_fiscale";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Persona</title>
</head>
<body>
    <h1>Modifica Persona</h1>
    <?php if ($messaggio): ?>
        <p><?php echo htmlspecialchars($messaggio); ?></p>
    <?php endif; ?>

    <?php if (!$persona_trovata): ?>
        <form method="POST">
            <label for="codice_fiscale">Codice Fiscale:</label>
            <input type="text" name="codice_fiscale" id="codice_fiscale" maxlength="16" required style="text-transform:uppercase;">
            <button type="submit" name="cerca">Cerca</button>
        </form>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="codice_fiscale" value="<?php echo htmlspecialchars($persona_trovata['codice_fiscale']); ?>">
            <label for="nome">Nome:</label>
            <input type="text" name="nome" id="nome" required value="<?php echo htmlspecialchars($persona_trovata['nome']); ?>"><br>
            <label for="cognome">Cognome:</label>
            <input type="text" name="cognome" id="cognome" required value="<?php echo htmlspecialchars($persona_trovata['cognome']); ?>"><br>
            <label for="data_nascita">Data di Nascita:</label>
            <input type="date" name="data_nascita" id="data_nascita" required value="<?php echo htmlspecialchars($persona_trovata['data_nascita']); ?>"><br>
            <button type="submit" name="modifica">Salva Modifiche</button>
        </form>
    <?php endif; ?>

    <br>
    <a href="../dashboard.php">Torna alla Dashboard</a>
</body>
</html>
