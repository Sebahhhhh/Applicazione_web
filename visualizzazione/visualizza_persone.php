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
$filtro_cognome = isset($_GET['cognome']) ? trim($_GET['cognome']) : '';
$filtro_data_dopo = isset($_GET['data_dopo']) ? trim($_GET['data_dopo']) : '';

// Applica filtri
$persone_filtrate = $persone;
if (!empty($filtro_cognome) || !empty($filtro_data_dopo)) {
    $persone_filtrate = array_filter($persone, function($persona) use ($filtro_cognome, $filtro_data_dopo) {
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
