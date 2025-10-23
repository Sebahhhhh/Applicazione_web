<?php
require_once '../config.php';
// verifica che l'utente sia autenticato prima di mostrare la pagina
verifica_autenticazione();

// inizializza variabili per messaggio ed esito
$messaggio = '';
$tipo_messaggio = '';

// gestisce la richiesta di inserimento persona
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // prende e normalizza i dati dal form
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $data_nascita = trim($_POST['data_nascita']);

    // controlla che tutti i campi siano compilati
    if (empty($codice_fiscale) || empty($nome) || empty($cognome) || empty($data_nascita)) {
        // mostra errore se mancano campi
        $messaggio = "Tutti i campi sono obbligatori";
        $tipo_messaggio = 'error';
    }
    // valida il formato del codice fiscale
    elseif (!valida_codice_fiscale($codice_fiscale)) {
        // mostra errore se il formato è errato
        $messaggio = "Formato codice fiscale non valido";
        $tipo_messaggio = 'error';
    }
    // verifica che il codice fiscale non sia già presente
    elseif (codice_fiscale_esiste($codice_fiscale)) {
        // mostra errore se il codice fiscale è già presente
        $messaggio = "Codice fiscale già esistente";
        $tipo_messaggio = 'error';
    }
    else {
        // aggiunge la persona al file json
        if (aggiungi_persona($codice_fiscale, $nome, $cognome, $data_nascita)) {
            // mostra conferma di successo se la persona è stata aggiunta
            $messaggio = "Persona aggiunta.";
            $tipo_messaggio = 'success';
        } else {
            // gestisce eventuali errori di salvataggio
            $messaggio = "Errore nel salvataggio";
            $tipo_messaggio = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Persona</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Aggiungi Persona</h1>

        <?php if ($messaggio): ?>
            <!-- mostra messaggio di errore o successo -->
            <p class="<?php echo $tipo_messaggio; ?>"><?php echo htmlspecialchars($messaggio); ?></p>
        <?php endif; ?>

        <!-- form per l'inserimento dei dati della persona -->
        <form action="" method="POST" class="form-persona">
            <div class="form-group">
                <label for="codice_fiscale">Codice Fiscale:</label>
                <!-- pattern per validare il formato del codice fiscale -->
                <input type="text" id="codice_fiscale" name="codice_fiscale"
                       pattern="[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]"
                       maxlength="16" required style="text-transform: uppercase;">
            </div>

            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>

            <div class="form-group">
                <label for="cognome">Cognome:</label>
                <input type="text" id="cognome" name="cognome" required>
            </div>

            <div class="form-group">
                <label for="data_nascita">Data di Nascita:</label>
                <input type="date" id="data_nascita" name="data_nascita" required>
            </div>

            <button type="submit">Salva Persona</button>
        </form>

        <a href="../dashboard.php" class="back-link">Torna alla Dashboard</a>
    </div>
</body>
</html>
