<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login/login.html');
    exit;
}

// Leggi il file JSON
$file = 'data/persone.json';
$persone = [];

if (file_exists($file)) {
    $json_data = file_get_contents($file);
    $persone = json_decode($json_data, true);

    if ($persone === null) {
        $persone = [];
    }
}

// Gestione filtri
$filtro_nome = isset($_GET['nome']) ? trim($_GET['nome']) : '';
$filtro_cognome = isset($_GET['cognome']) ? trim($_GET['cognome']) : '';
$filtro_cf = isset($_GET['codice_fiscale']) ? trim($_GET['codice_fiscale']) : '';

// Applica filtri
$persone_filtrate = $persone;
if (!empty($filtro_nome) || !empty($filtro_cognome) || !empty($filtro_cf)) {
    $persone_filtrate = array_filter($persone, function($persona) use ($filtro_nome, $filtro_cognome, $filtro_cf) {
        $match = true;

        if (!empty($filtro_nome)) {
            $match = $match && stripos($persona['nome'], $filtro_nome) !== false;
        }

        if (!empty($filtro_cognome)) {
            $match = $match && stripos($persona['cognome'], $filtro_cognome) !== false;
        }

        if (!empty($filtro_cf)) {
            $match = $match && stripos($persona['codice_fiscale'], $filtro_cf) !== false;
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
<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login/login.html');
    exit;
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

        <?php if (isset($_GET['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <p class="success">Persona aggiunta con successo!</p>
        <?php endif; ?>

        <form action="../inserimento/salva_persona.php" method="POST" class="form-persona">
            <div class="form-group">
                <label for="codice_fiscale">Codice Fiscale:</label>
                <input type="text" id="codice_fiscale" name="codice_fiscale"
                       pattern="[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]"
                       maxlength="16" required style="text-transform: uppercase;">
                <small>Formato: 16 caratteri (es. RSSMRA80A01H501U)</small>
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

