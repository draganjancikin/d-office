<?php
use Roloffice\Core\Database;

// edit contact of client
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editContact"])) {

    $contact_id = $_POST["contact_id"];
    $client_id = basicValidation($_GET["client_id"]);
    $contacttype_id = $_POST["contacttype_id"];
    $number = basicValidation($_POST["number"]);
    $note = basicValidation($_POST["note"]);

    $db = new Database();
    
    $db->connection->query("UPDATE contacts "
                    . " SET type_id='$contacttype_id', number='$number', note='$note' "
                    . " WHERE id = '$contact_id' ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}
