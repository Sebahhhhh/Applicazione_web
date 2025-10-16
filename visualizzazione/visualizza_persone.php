<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: ../login/login.php');
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

// Gestione filtri

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
                <input type="text" name="nome" placeholder="Nome" value="<?php echo htmlspecialchars($filtro_nome); ?>">
                <input type="text" name="cognome" placeholder="Cognome" value="<?php echo htmlspecialchars($filtro_cognome); ?>">
                <input type="text" name="codice_fiscale" placeholder="Codice Fiscale" value="<?php echo htmlspecialchars($filtro_cf); ?>">
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
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($persona['data_nascita']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="totale">Totale persone visualizzate: <?php echo count($persone_filtrate); ?> / <?php echo count($persone); ?></p>
        <?php endif; ?>

        <a href="../dashboard.php" class="back-link">Torna alla Dashboard</a>
    </div>
</body>
</html>
