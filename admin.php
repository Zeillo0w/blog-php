<?php
session_start();

include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $contents = $_POST['contents'];
    $authorFirstName = $_POST['author_first_name'];
    $authorLastName = $_POST['author_last_name'];
    $categoryName = $_POST['category_name'];

    $authorQuery = $conn->prepare("SELECT Id FROM author WHERE FirstName = ? AND LastName = ?");
    $authorQuery->bind_param("ss", $authorFirstName, $authorLastName);
    $authorQuery->execute();
    $authorResult = $authorQuery->get_result();

    if ($authorResult->num_rows > 0) {
        $authorRow = $authorResult->fetch_assoc();
        $authorId = $authorRow['Id'];
    } else {
        $insertAuthorQuery = $conn->prepare("INSERT INTO author (FirstName, LastName) VALUES (?, ?)");
        $insertAuthorQuery->bind_param("ss", $authorFirstName, $authorLastName);
        $insertAuthorQuery->execute();
        $authorId = $conn->insert_id;
        $insertAuthorQuery->close();
    }

    $categoryQuery = $conn->prepare("SELECT Id FROM category WHERE Name = ?");
    $categoryQuery->bind_param("s", $categoryName);
    $categoryQuery->execute();
    $categoryResult = $categoryQuery->get_result();

    if ($categoryResult->num_rows > 0) {
        $categoryRow = $categoryResult->fetch_assoc();
        $categoryId = $categoryRow['Id'];
    } else {
        $insertCategoryQuery = $conn->prepare("INSERT INTO category (Name) VALUES (?)");
        $insertCategoryQuery->bind_param("s", $categoryName);
        $insertCategoryQuery->execute();
        $categoryId = $conn->insert_id;
        $insertCategoryQuery->close();
    }

    $insertQuery = $conn->prepare("INSERT INTO post (Title, Contents, Author_Id, Category_Id, CreationTimestamp) VALUES (?, ?, ?, ?, NOW())");
    $insertQuery->bind_param("ssii", $title, $contents, $authorId, $categoryId);
    $insertResult = $insertQuery->execute();

    if ($insertResult) {
        echo "Nouvel article ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout de l'article: " . $conn->error;
    }

    $insertQuery->close();
    $categoryQuery->close();
    $authorQuery->close();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Mon Blog</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<header>
    <h1>Page Admin</h1>
    <a href="logout.php" class="logout">Déconnexion</a>
</header>

<div class="container">
    <h2>Ajouter un article</h2>
    <form method="post" action="admin.php" class="form-card">
        <label for="title">Titre:</label>
        <input type="text" name="title" required><br>

        <label for="contents">Contenu:</label>
        <textarea name="contents" required></textarea><br>

        <label for="author_first_name">Prénom de l'auteur:</label>
        <input type="text" name="author_first_name" required><br>

        <label for="author_last_name">Nom de l'auteur:</label>
        <input type="text" name="author_last_name" required><br>

        <label for="category_name">Nom de la catégorie:</label>
        <input type="text" name="category_name" required><br>

        <input type="submit" value="Ajouter un article">
    </form>

    <h2>Liste des articles</h2>
    <table class="article-table">
        <tr>
            <th>Titre</th>
            <th>Contenu</th>
            <th>Catégorie</th>
            <th>Auteur</th>
            <th>Action</th>
        </tr>
        <?php
        $selectQuery = $conn->prepare("SELECT post.Id, post.Title, post.Contents, post.CreationTimestamp, author.FirstName, author.LastName, category.Name
                                      FROM post
                                      INNER JOIN author ON post.Author_Id = author.Id
                                      INNER JOIN category ON post.Category_Id = category.Id");
        $selectQuery->execute();
        $result = $selectQuery->get_result();

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Title'] . "</td>";
            echo "<td class='article-content' data-content='" . htmlentities($row['Contents'], ENT_QUOTES, 'UTF-8') . "'>" . substr($row['Contents'], 0, 50) . "...</td>";
            echo "<td>" . $row['Name'] . "</td>";
            echo "<td>" . $row['FirstName'] . " " . $row['LastName'] . "</td>";
            echo "<td><a href='edit_article.php?id=" . $row['Id'] . "'>Modifier</a> | <a href='delete_article.php?id=" . $row['Id'] . "'>Supprimer</a></td>";
            echo "</tr>";
        }

        $selectQuery->close();
        ?>
    </table>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        var articleContents = document.querySelectorAll('.article-content');

        articleContents.forEach(function (element) {
            element.addEventListener('click', function () {
                var content = element.getAttribute('data-content');
                alert(content);
            });
        });
    });
</script>

</body>
</html>
