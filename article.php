<?php
include('db.php');

if (isset($_GET['id'])) {
    $articleId = $_GET['id'];
    $query = "SELECT * FROM post WHERE Id = $articleId";
    $result = $conn->query($query);
    $article = $result->fetch_assoc();

    // Récupérer les commentaires
    $queryComments = "SELECT * FROM comment WHERE Post_Id = $articleId";
    $resultComments = $conn->query($queryComments);
}

// Traitement du formulaire d'ajout de commentaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = htmlspecialchars($_POST['nickname']);
    $commentContents = htmlspecialchars($_POST['contents']);

    // Ajouter le commentaire à la base de données
    $insertCommentQuery = $conn->prepare("INSERT INTO comment (NickName, Contents, CreationTimestamp, Post_Id) VALUES (?, ?, NOW(), ?)");
    $insertCommentQuery->bind_param("ssi", $nickname, $commentContents, $articleId);
    $insertCommentResult = $insertCommentQuery->execute();

    if ($insertCommentResult) {
        // Rafraîchir la page après l'ajout du commentaire
        header("Location: article.php?id=$articleId");
        exit();
    } else {
        echo "Erreur lors de l'ajout du commentaire: " . $conn->error;
    }

    // Fermer la requête préparée
    $insertCommentQuery->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails de l'article - Mon Blog</title>
    <link rel="stylesheet" href="article.css">
</head>
<body>
    <header>
        <h1>Titre de l'article : <?php echo $article['Title']; ?></h1>
        <a href="index.php">Retour à l'accueil</a>
    </header>
<div class="container">

    <div class="article-card">
        <p class="article-content"><?php echo $article['Contents']; ?></p>
        <div class="article-meta">
            <p><strong>Auteur:</strong> <?php echo $article['Author_Id']; ?></p>
            <p><strong>Date de post:</strong> <?php echo $article['CreationTimestamp']; ?></p>
        </div>
    </div>

<!-- Afficher les commentaires -->
<div class="comment-card">
    <h2>Commentaires</h2>
    <?php
    while ($comment = $resultComments->fetch_assoc()) {
        echo "<div class='comment'>";
        echo "<p><strong class='commenter-name'>" . $comment['NickName'] . ":</strong> " . $comment['Contents'] . "</p>";
        echo "</div>";
    }
    ?>
</div>


    <!-- Formulaire d'ajout de commentaire -->
    <div class="comment-form">
        <h2>Ajouter un commentaire</h2>
        <form action="article.php?id=<?php echo $articleId; ?>" method="post">
            <label for="nickname">Nom:</label>
            <input type="text" name="nickname" required><br>
            <label for="contents">Commentaire:</label>
            <textarea name="contents" required></textarea><br>
            <input type="submit" value="Ajouter un commentaire">
        </form>
    </div>

</div>

</body>
</html>
