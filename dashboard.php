<?php
require_once 'config.php';
verifica_autenticazione();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Benvenuto, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <h2>Gestione Anagrafica Persone</h2>
        <nav class="dashboard-nav">
            <ul>
                <li><a href="inserimento/aggiungi_persona.php">Aggiungi Persona</a></li>
                <li><a href="visualizzazione/visualizza_persone.php">Visualizza Persone</a></li>
                <li><a href="eliminazione/elimina_persona.php">Elimina Persona</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
