<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editId = $_POST['edit_id'];
    $title = $_POST['title'];
    $contents = $_POST['contents'];
    $authorId = $_POST['author'];
    $categoryId = $_POST['category'];

    $updateQuery = $conn->prepare("UPDATE post SET Title = ?, Contents = ?, Author_Id = ?, Category_Id = ? WHERE Id = ?");
    $updateQuery->bind_param("ssiii", $title, $contents, $authorId, $categoryId, $editId);
    $updateResult = $updateQuery->execute();

    if ($updateResult) {
        echo "Article modifié avec succès.";
        // Redirect page admin apres 2 sec
        header("refresh:2;url=admin.php");
        exit();
    } else {
        echo "Erreur lors de la modification de l'article: " . $conn->error;
    }
    
    $updateQuery->close();
}

if (isset($_GET['id'])) {
    $articleId = $_GET['id'];

    $selectQuery = $conn->prepare("SELECT * FROM post WHERE Id = ?");
    $selectQuery->bind_param("i", $articleId);
    $selectQuery->execute();
    $result = $selectQuery->get_result();

    if ($result->num_rows > 0) {
        $article = $result->fetch_assoc();
    } else {
        echo "L'article demandé n'existe pas.";
        exit();
    }

    $selectQuery->close();
} else {
    echo "Aucun identifiant d'article fourni.";
    exit();
}

// liste des auteurs pour le formulaire
$authorListQuery = $conn->prepare("SELECT Id, FirstName, LastName FROM author");
$authorListQuery->execute();
$authorListResult = $authorListQuery->get_result();

// Catégories pour le formulaire
$categoryListQuery = $conn->prepare("SELECT Id, Name FROM category");
$categoryListQuery->execute();
$categoryListResult = $categoryListQuery->get_result();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'article - Mon Blog</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header>
    <h1>Page Admin</h1>
    <a href="logout.php" class="logout">Déconnexion</a>
</header>

<div class="container">
    <h2>Modifier l'article</h2>
    <form method="post" action="edit_article.php" class="form-card">
        <input type="hidden" name="edit_id" value="<?php echo $articleId; ?>">

        <label for="title">Titre:</label>
        <input type="text" name="title" value="<?php echo $article['Title']; ?>" required><br>

        <label for="contents">Contenu:</label>
        <textarea name="contents" required><?php echo $article['Contents']; ?></textarea><br>

        <label for="author">Auteur:</label>
        <select name="author" required>
            <?php while ($author = $authorListResult->fetch_assoc()) : ?>
                <option value="<?php echo $author['Id']; ?>" <?php if ($author['Id'] === $article['Author_Id']) echo "selected"; ?>>
                    <?php echo $author['FirstName'] . " " . $author['LastName']; ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <label for="category">Catégorie:</label>
        <select name="category" required>
            <?php while ($category = $categoryListResult->fetch_assoc()) : ?>
                <option value="<?php echo $category['Id']; ?>" <?php if ($category['Id'] === $article['Category_Id']) echo "selected"; ?>>
                    <?php echo $category['Name']; ?>
                </option>
            <?php endwhile; ?>
        </select><br>

        <input type="submit" value="Modifier l'article">
    </form>
</div>

</body>
</html>
