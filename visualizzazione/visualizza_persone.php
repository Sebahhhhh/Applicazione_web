<?php
require_once '../config.php';
// verifica che l'utente sia autenticato prima di mostrare la pagina
verifica_autenticazione();

// prende i filtri dal form GET
$filtro_cognome = isset($_GET['cognome']) ? trim($_GET['cognome']) : '';
$filtro_data_dopo = isset($_GET['data_dopo']) ? trim($_GET['data_dopo']) : '';

// ottiene le persone filtrate in base ai parametri
$persone_filtrate = ottieni_persone($filtro_cognome, $filtro_data_dopo);
// carica tutte le persone dal file JSON per il conteggio totale
$tutte_persone = leggi_json(PERSONE_FILE);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza Persone</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Elenco Persone</h1>

        <div class="filtri">
            <h3>Filtri di Ricerca</h3>
            <form method="GET" class="form-filtri">
                <input type="text" name="cognome" placeholder="Cognome" value="<?php echo htmlspecialchars($filtro_cognome); ?>">
                <label for="data_dopo">Nati dopo il:</label>
                <input type="date" name="data_dopo" id="data_dopo" value="<?php echo htmlspecialchars($filtro_data_dopo); ?>">
                <button type="submit">Cerca</button>
                <a href="visualizza_persone.php" class="btn-reset">Reset</a>
            </form>
        </div>

        <?php if (empty($persone_filtrate)): ?>
            <p class="no-data">Nessuna persona trovata.</p>
        <?php else: ?>
            <table class="tabella-persone">
                <thead>
                    <tr>
                        <th>Codice Fiscale</th>
                        <th>Nome</th>
                        <th>Cognome</th>
                        <th>Data di Nascita</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($persone_filtrate as $persona): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($persona['codice_fiscale']); ?></td>
                            <td><?php echo htmlspecialchars($persona['nome']); ?></td>
                            <td><?php echo htmlspecialchars($persona['cognome']); ?></td>
                            <td><?php echo formatta_data($persona['data_nascita']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="totale">Totale persone visualizzate: <?php echo count($persone_filtrate); ?> / <?php echo count($tutte_persone); ?></p>
        <?php endif; ?>

        <a href="../dashboard.php" class="back-link">Torna alla Dashboard</a>
    </div>
</body>
</html>
