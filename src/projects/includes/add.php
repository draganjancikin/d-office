<?php
// novi projekat
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['add']) ) {

    $date = date('Y-m-d h:i:s');
    $user_id = $_SESSION['user_id'];

    $client_id = htmlspecialchars($_POST["client_id"]);
    $title = htmlspecialchars($_POST['title']);
    // $priority_id = htmlspecialchars($_POST['priority_id']);
    $priority_id = 2;
    // $note = htmlspecialchars($_POST['note']);
    $note = "";

    $db = new Connection();

    $db->connection->query("INSERT INTO project (date, created_at_user_id, client_id, title, priority_id, note, status) VALUES ( '$date','$user_id', '$client_id', '$title', '$priority_id', '$note', '1' )") or die(mysqli_error($db->connection));

    $project_id = $db->connection->insert_id;

    $pr_id = $project->setPrId($project_id);

    // If exist $_POST['pidb_id'] 
    if(isset($_POST['pidb_id'])){
        // update project_id in pidb
        $pidb_id = $_POST['pidb_id'];
        $db->connection->query("UPDATE pidb SET project_id='$project_id' WHERE id = '$pidb_id' ") or die(mysqli_error($connection));
    }

    die('<script>location.href = "?view&project_id=' .$project_id. '" </script>');
}

// nova beleška
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['addNote']) ) {

    $date = date('Y-m-d h:i:s');
    $project_id = $_GET["project_id"];
    $user_id = $_SESSION['user_id'];

    $note = htmlspecialchars($_POST['note']);

    $db = new Connection();

    $db->connection->query("INSERT INTO project_note (date, project_id, created_at_user_id, note) VALUES ( '$date','$project_id', '$user_id', '$note' )") or die(mysqli_error($db->connection));

    // ovde link da vodi na pregled projekta
    die('<script>location.href = "?view&project_id=' .$project_id. '" </script>');
}

// novi zadatak
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['addTask']) ) {

    $date = date('Y-m-d h:i:s');
    $project_id = $_GET["project_id"];
    $status_id = $_POST["status_id"];
    $user_id = $_SESSION['user_id'];

    $tip_id = $_POST["tip_id"];
    $title = htmlspecialchars($_POST['title']);

    $db = new Connection();

    $db->connection->query("INSERT INTO project_task (date, project_id, created_at_user_id, tip_id, status_id, title) VALUES ( '$date','$project_id', '$user_id', '$tip_id', '$status_id', '$title' )") or die(mysqli_error($db->connection));

    // ovde link da vodi na pregled projekta
    die('<script>location.href = "?view&project_id=' .$project_id. '" </script>');
}

// nova beleška uz zadatak
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['addTaskNote']) ) {

    $date = date('Y-m-d h:i:s');
    $project_id = $_GET["project_id"];
    $task_id = $_GET["task_id"];
    $user_id = $_SESSION['user_id'];

    $note = htmlspecialchars($_POST['note']);

    $db = new Connection();

    $db->connection->query("INSERT INTO project_task_note (date, project_task_id, created_at_user_id, note) VALUES ( '$date','$task_id', '$user_id', '$note' )") or die(mysqli_error($db->connection));

    // ovde link da vodi na pregled zadatka
    die('<script>location.href = "?editTask&task_id=' .$task_id. '&project_id=' .$project_id. '" </script>');
}

// dodavanje fajla
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['addFile']) ) {

    // echo 'upload u toku ...<br />';
  
    $project_id = htmlspecialchars($_GET['project_id']);

    // echo 'projekat' .$project_id;

    if ($_FILES["file"]["error"] > 0){

        if ($_FILES["file"]["error"]==4){ 
            echo"Molimo izaberite fajl";
        }elseif($_FILES["file"]["error"]==1){
            echo"Fajl koji ste izabrali je prevelik!";
        }else{
            echo "Greška: " . $_FILES["file"]["error"] . "<br>";
        }

    }else{

        // echo "Upload: " . $_FILES["file"]["name"] . "<br>";

        // $target_file = $target_dir . preg_replace("/[^a-z0-9\_\-\.]/i", '', $_FILES['file']["name"]);

        // echo "Type: " . $_FILES["file"]["type"] . "<br>";
        // echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
        // echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";

        // provera dali postoji folder određenog naloga, ako ne postoji pravljenje foldera
        if (!is_dir('upload/project_id_'.$project_id)) {
            mkdir('upload/project_id_'.$project_id);
        }

        $path = 'upload/project_id_'.$project_id.'/';

        // echo 'putanja do fajla je: '.$path.'<br/>';
        if (file_exists($path . $_FILES["file"]["name"])){
            echo $_FILES["file"]["name"] . " already exists. ";
        }else{
            // echo $_FILES["file"]["tmp_name"];
            if( move_uploaded_file($_FILES["file"]["tmp_name"], $path . $_FILES["file"]["name"]) ) {
                echo "jeah";
            } else {
                echo "no";
            }

            // echo "Sačuvano u: " . $path . $_FILES["file"]["name"];
        }
        // exit();    

    die('<script>location.href = "?view&project_id=' .$project_id. '" </script>');
}

}
