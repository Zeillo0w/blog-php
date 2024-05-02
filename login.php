<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérifier les informations de connexion ((pas sécur pour ce projet)
    if ($username === 'root' && $password === 'root') {
        $_SESSION['logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Mon Blog</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="card">

    <?php
    if (isset($error)) {
        echo "<p class='error'>$error</p>";
    }
    ?>

    <form method="post" action="login.php">
        <label for="username">Nom d'utilisateur:</label>
        <input type="text" name="username" required><br>
        <label for="password">Mot de passe:</label>
        <input type="password" name="password" required><br>
        <input type="submit" value="Se connecter">
        <a href="index.php">Retour à l'accueil</a>
    </form>

</div>

</body>
</html>
