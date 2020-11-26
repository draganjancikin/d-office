<?php
// brisanje beleške iz projekta
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['delNote']) ) {

    $note_id = htmlspecialchars($_GET["note_id"]);
    $project_id = htmlspecialchars($_GET["project_id"]);

    $db = new Connection();

    $db->connection->query("DELETE FROM project_note WHERE id='$note_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&project_id='.$project_id.'" </script>');
}

// brisanje zadatka iz projekta
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['delTask']) ) {

    $task_id = htmlspecialchars($_GET["task_id"]);
    $project_id = htmlspecialchars($_GET["project_id"]);

    $db = new Connection();

    // brisanje beležaka koje su vezane uz zadatak
    $result_project_task_notes = $db->connection->query("SELECT * FROM project_task_note WHERE project_task_id='$task_id'") or die(mysqli_error($db->connection));
    while($row_project_task_note = mysqli_fetch_array($result_project_task_notes)){
        $project_task_note_id = $row_project_task_note['id'];
        // sledeća metoda briše i artikal iz pidb_article i property-e iz pidb_article_property
        $project->delNoteFromProjectTask($project_task_note_id);
    }

    $db->connection->query("DELETE FROM project_task WHERE id='$task_id' ") or die(mysqli_error($db->connection));
  
    die('<script>location.href = "?view&project_id='.$project_id.'" </script>');
}

// brisanje beleške iz iz zadatka
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET['delTaskNote']) ) {

    $task_note_id = htmlspecialchars($_GET["task_note_id"]);
    $project_id = htmlspecialchars($_GET["project_id"]);
    $task_id = htmlspecialchars($_GET["task_id"]);

    $db = new Connection();

    $db->connection->query("DELETE FROM project_task_note WHERE id='$task_note_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?editTask&project_id='.$project_id.'&task_id='.$task_id.'" </script>');
}
