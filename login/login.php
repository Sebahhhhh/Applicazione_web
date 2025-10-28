<?php
require_once '../config.php';

inizializza_sessione();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Tutti i campi sono obbligatori.";
    } elseif (verifica_credenziali($username, $password)) {
        $_SESSION['username'] = $username;
        header('Location: ../dashboard.php');
        exit;
    } else {
        $error = "Credenziali non valide!";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" class="form-persona">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Accedi</button>
        </form>

        <p><a href="register.php">Registrati</a></p>
        <p><a href="../index.html">Torna alla home</a></p>
    </div>
</body>
</html>
