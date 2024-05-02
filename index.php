<?php
include('db.php');

$query = "SELECT * FROM post ORDER BY CreationTimestamp DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Accueil - Mon Blog</title>
    <link rel="stylesheet" href="index.css">
</head>

<body>
    <div class="nav-wrapper">
        <header>

            <h1>YNOV BLOG</h1>
            <div className='tech-icon'>
                <a href="login.php" class="login">Connexion Admin</a>            </div>
        </header>
    </div>

    <!-- Affichage des articles -->
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<div>";
        echo "<h2><a href='article.php?id=" . $row['Id'] . "'>" . $row['Title'] . "</a></h2>";
        echo "<p>" . substr($row['Contents'], 0, 100) . "...</p>";
        echo "</div>";
    }
    ?>

</body>

</html>