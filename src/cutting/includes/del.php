<?php
$user_id = $_SESSION['user_id'];
$date = date('Y-m-d h:i:s');

// dell article from cutting
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delCuttingArticle"])) {
    $cutting_fence_id = htmlspecialchars($_GET['cutting_fence_id']);
    $cutting_fence_article_id = htmlspecialchars($_GET['cutting_fence_article_id']);

    $db = new DB();
    $connection = $db->connectDB();

    $connection->query("DELETE FROM cutting_fence_article WHERE id='$cutting_fence_article_id' ") or die(mysqli_error($connection));

    die('<script>location.href = "?edit&cutting_id='.$cutting_fence_id.'" </script>');
}


// del cutting
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delCutting"])) { 

    $cutting_id = htmlspecialchars($_GET["cutting_id"]);

    $db = new DB();
    $connection = $db->connectDB();

    // first: delete articles from cutting_fence_article
    $result_article = $connection->query("SELECT * FROM cutting_fence_article WHERE cutting_fence_id='$cutting_id'") or die(mysqli_error($connection));

    while($row_article = mysqli_fetch_array($result_article)){
        $connection->query("DELETE FROM cutting_fence_article WHERE cutting_fence_id='$cutting_id' ") or die(mysqli_error($connection));
    }

    // second: delete cutting
    $connection->query("DELETE FROM cutting_fence WHERE id='$cutting_id' ") or die(mysqli_error($connection));

    die('<script>location.href = "?name=&search=" </script>');
}
