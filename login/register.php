<?php
require_once '../config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $error = 'Tutti i campi sono obbligatori.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Le password non corrispondono.';
    } elseif (username_esiste($username)) {
        $error = 'Nome utente già preso.';
    } else {
        if (aggiungi_utente($username, $password)) {
            $success = 'Registrazione avvenuta con successo. Puoi ora effettuare il login.';
        } else {
            $error = 'Errore durante la registrazione!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Registrazione</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST" class="form-persona">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Conferma Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Registrati</button>
        </form>

        <p><a href="login.php">Accedi</a></p>
        <p><a href="../index.html">Torna alla home</a></p>
    </div>
</body>
</html>
