<?php

// del cutting
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delCutting"])) { 

    $cutting_id = htmlspecialchars($_GET["cutting_id"]);

    $db = new Database();

    // first: delete articles from cutting_fence_article
    $result_article = $db->connection->query("SELECT * FROM cutting_fence_article WHERE cutting_fence_id='$cutting_id'") or die(mysqli_error($db->connection));

    while($row_article = mysqli_fetch_array($result_article)){
        $db->connection->query("DELETE FROM cutting_fence_article WHERE cutting_fence_id='$cutting_id' ") or die(mysqli_error($db->connection));
    }

    // second: delete cutting
    $db->connection->query("DELETE FROM cutting_fence WHERE id='$cutting_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?name=&search=" </script>');
}
