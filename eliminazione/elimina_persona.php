<?php
require_once '../config.php';
verifica_autenticazione();

$messaggio = '';
$tipo_messaggio = '';

$persone = leggi_json(PERSONE_FILE);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['elimina'])) {
    $codice_fiscale = strtoupper(trim($_POST['codice_fiscale']));

    if (codice_fiscale_esiste($codice_fiscale)) {
        if (elimina_persona($codice_fiscale)) {
            $messaggio = "Persona con codice fiscale $codice_fiscale eliminata con successo!";
            $tipo_messaggio = 'success';
            $persone = leggi_json(PERSONE_FILE);
        } else {
            $messaggio = "Errore nel salvataggio del file";
            $tipo_messaggio = 'error';
        }
    } else {
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

        <?php if (!empty($messaggio)): ?>
            <div class="messaggio <?php echo $tipo_messaggio; ?>">
                <?php echo htmlspecialchars($messaggio); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-elimina">
            <label for="codice_fiscale">Codice Fiscale da eliminare:</label>
            <input type="text" name="codice_fiscale" id="codice_fiscale" required maxlength="16" style="text-transform: uppercase;">
            <button type="submit" name="elimina">Elimina Persona</button>
        </form>

        <h2>Persone Registrate</h2>
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
