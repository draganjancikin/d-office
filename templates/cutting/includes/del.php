<?php
$user_id = $_SESSION['user_id'];
$date = date('Y-m-d h:i:s');

// dell article from cutting
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delCuttingArticle"])) {
    $cutting_fence_id = htmlspecialchars($_GET['cutting_fence_id']);
    $cutting_fence_article_id = htmlspecialchars($_GET['cutting_fence_article_id']);

    $db = new \Roloffice\Controller\DatabaseController();

    $db->connection->query("DELETE FROM cutting_fence_article WHERE id='$cutting_fence_article_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?edit&cutting_id='.$cutting_fence_id.'" </script>');
}


// del cutting
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delCutting"])) { 

    $cutting_id = htmlspecialchars($_GET["cutting_id"]);

    $db = new \Roloffice\Controller\DatabaseController();

    // first: delete articles from cutting_fence_article
    $result_article = $db->connection->query("SELECT * FROM cutting_fence_article WHERE cutting_fence_id='$cutting_id'") or die(mysqli_error($db->connection));

    while($row_article = mysqli_fetch_array($result_article)){
        $db->connection->query("DELETE FROM cutting_fence_article WHERE cutting_fence_id='$cutting_id' ") or die(mysqli_error($db->connection));
    }

    // second: delete cutting
    $db->connection->query("DELETE FROM cutting_fence WHERE id='$cutting_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?name=&search=" </script>');
}
