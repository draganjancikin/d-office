<?php

// Delete CuttingSheet
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delete"])) { 
  
  /**
   * Cutting Sheet ID
   * @var int
   */
  $cs_id = htmlspecialchars($_GET["cs_id"]);


  // Check if exist CuttingSheet.
  if ($cs = $entityManager->find("\App\Entity\CuttingSheet", $cs_id)) {

    // Check if exist Article in CuttingSheet.
    if ($cs_articles = $entityManager->getRepository('\App\Entity\CuttingSheetArticle')->getCuttingSheetArticles($cs_id)) {

      // Loop trough all Articles of CuttingSheet.
      foreach ($cs_articles as $cs_article) {
        // Remove Article.
        $entityManager->remove($cs_article);
        $entityManager->flush();
      }

    }

    // Remove CuttingSheet.
    $entityManager->remove($cs);
    $entityManager->flush();
  }

  die('<script>location.href = "?search=" </script>');
}
