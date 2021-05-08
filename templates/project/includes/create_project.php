<?php
// Create a new Project.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET['createProject']) ) {

  $date = date('Y-m-d h:i:s');
  $user_id = $_SESSION['user_id'];

  $client_id = htmlspecialchars($_POST["client_id"]);
  $title = htmlspecialchars($_POST['title']);
  // $priority_id = htmlspecialchars($_POST['priority_id']);
  $priority_id = 2;
  // $note = htmlspecialchars($_POST['note']);
  $note = "";

  $db = new Database();

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