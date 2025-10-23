<?php
require_once '../config.php';
// verifica che l'utente sia autenticato prima di mostrare la pagina
verifica_autenticazione();

// inizializza variabili per messaggio ed esito
$messaggio = '';
$tipo_messaggio = '';

// carica tutte le persone dal file json
$persone = leggi_json(PERSONE_FILE);

// gestisce la richiesta di eliminazione
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elimina'])) {
    // prende il codice fiscale dal form e lo normalizza
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));

    // controlla se il codice fiscale esiste tra le persone
    if (codice_fiscale_esiste($codice_fiscale)) {
        // elimina la persona e aggiorna il file json
        if (elimina_persona($codice_fiscale)) {
            $messaggio = "Persona con codice fiscale $codice_fiscale eliminata con successo!";
            $tipo_messaggio = 'success';
            // ricarica la lista aggiornata delle persone
            $persone = leggi_json(PERSONE_FILE);
        } else {
            // gestisce errore di salvataggio
            $messaggio = "Errore nel salvataggio del file";
            $tipo_messaggio = 'error';
        }
    } else {
        // gestisce caso in cui il codice fiscale non esiste
        $messaggio = "Nessuna persona trovata con il codice fiscale: $codice_fiscale";
        $tipo_messaggio = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elimina Persona</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Elimina Persona</h1>

        <!-- mostra il messaggio di esito se presente -->
        <?php if (!empty($messaggio)): ?>
            <div class="messaggio <?php echo $tipo_messaggio; ?>">
                <?php echo htmlspecialchars($messaggio); ?>
            </div>
        <?php endif; ?>

        <!-- form per inserire il codice fiscale da eliminare -->
        <form method="POST" class="form-elimina">
            <label for="codice_fiscale">Codice Fiscale da eliminare:</label>
            <input type="text" name="codice_fiscale" id="codice_fiscale" required maxlength="16" style="text-transform: uppercase;">
            <button type="submit" name="elimina">Elimina Persona</button>
        </form>

        <h2>Persone Registrate</h2>
        <!-- mostra la tabella delle persone registrate -->
        <?php if (empty($persone)): ?>
            <p>Nessuna persona registrata.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Codice Fiscale</th>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Data di Nascita</th>
                </tr>
                <?php foreach ($persone as $persona): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($persona['codice_fiscale']); ?></td>
                        <td><?php echo htmlspecialchars($persona['nome']); ?></td>
                        <td><?php echo htmlspecialchars($persona['cognome']); ?></td>
                        <!-- formatta la data di nascita -->
                        <td><?php echo formatta_data($persona['data_nascita']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <br>
        <a href="../dashboard.php">Torna alla Dashboard</a>
    </div>
</body>
</html>
