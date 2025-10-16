<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Leggi il file JSON degli utenti
    $usersFile = '../data/users.json';

    if (!file_exists($usersFile)) {
        $error = "Nessun utente registrato.";
    } else {
        $jsonData = file_get_contents($usersFile);
        $users = json_decode($jsonData, true);

        // Cerca la corrispondenza
        foreach ($users as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                header('Location: ../dashboard.php');
                exit;
            }
        }
        $error = "Credenziali non valide.";
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
