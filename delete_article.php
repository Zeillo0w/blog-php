<?php
session_start();

include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $articleId = $_GET['id'];

    // Récup l'auteur et la catégorie associé a l'article
    $getInfoQuery = $conn->prepare("SELECT Author_Id, Category_Id FROM post WHERE Id = ?");
    $getInfoQuery->bind_param("i", $articleId);
    $getInfoQuery->execute();
    $infoResult = $getInfoQuery->get_result();

    if ($infoResult->num_rows > 0) {
        $infoRow = $infoResult->fetch_assoc();
        $authorId = $infoRow['Author_Id'];
        $categoryId = $infoRow['Category_Id'];

        // Supp l'article de la base de données
        $deleteQuery = $conn->prepare("DELETE FROM post WHERE Id = ?");
        $deleteQuery->bind_param("i", $articleId);
        $deleteResult = $deleteQuery->execute();

        if ($deleteResult) {
            echo "Article supprimé avec succès.";

            // Supprimer l'auteur s'il n'est pas partagé par d'autres articles
            $checkAuthorQuery = $conn->prepare("SELECT COUNT(*) as count FROM post WHERE Author_Id = ?");
            $checkAuthorQuery->bind_param("i", $authorId);
            $checkAuthorQuery->execute();
            $authorCountResult = $checkAuthorQuery->get_result();
            $authorCount = $authorCountResult->fetch_assoc()['count'];
         
            if ($authorCount == 0) {
                $deleteAuthorQuery = $conn->prepare("DELETE FROM author WHERE Id = ?");
                $deleteAuthorQuery->bind_param("i", $authorId);
                $deleteAuthorQuery->execute();
                echo "Auteur supprimé avec succès.";
            }

            // Supp la catégorie si pas partagée par d'autres articles
            $checkCategoryQuery = $conn->prepare("SELECT COUNT(*) as count FROM post WHERE Category_Id = ?");
            $checkCategoryQuery->bind_param("i", $categoryId);
            $checkCategoryQuery->execute();
            $categoryCountResult = $checkCategoryQuery->get_result();
            $categoryCount = $categoryCountResult->fetch_assoc()['count'];

            if ($categoryCount == 0) {
                $deleteCategoryQuery = $conn->prepare("DELETE FROM category WHERE Id = ?");
                $deleteCategoryQuery->bind_param("i", $categoryId);
                $deleteCategoryQuery->execute();
                echo "Catégorie supprimée avec succès.";
            }
        } else {
            echo "Erreur lors de la suppression de l'article: " . $deleteQuery->error;
        }

        // Fermer les requêtes 
        $deleteQuery->close();
        $getInfoQuery->close();
        $checkAuthorQuery->close();
        $checkCategoryQuery->close();
    }
}

header("Location: admin.php");
exit();
?>
