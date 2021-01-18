<?php
use Roloffice\Core\Database;

// edit client
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editClient"])) {

    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');

    $client_id = $_POST["client_id"];
    $type_id = $_POST["type_id"];
    $name = basicValidation($_POST["name"]);
    $name_note = basicValidation($_POST["name_note"]);

    if ( isset($_POST["is_supplier"]) ) {
        $is_supplier = $_POST["is_supplier"];
    } else {
        $is_supplier = 0;
    }

    $state_id = $_POST["state_id"];
    $city_id = $_POST["city_id"];
    $street_id = $_POST["street_id"];
    $home_number = basicValidation($_POST["home_number"]);
    $address_note = basicValidation($_POST["address_note"]);
    $note = basicValidation($_POST["note"]);

    $db = new Database();
    
    if ($type_id == 1){
        $db->connection->query(" UPDATE client " 
                         . " SET type_id='$type_id', name='$name', name_note='$name_note', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', address_note='$address_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                         . " WHERE id = '$client_id' ") or die(mysqli_error($db->connection));
    }

    if ($type_id == 2){
        if ( $type_id == 2 AND !isset($_POST["pib"]) ) {
            $db->connection->query("UPDATE client "
                            . " SET type_id='$type_id', name='$name', name_note='$name_note', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', address_note='$address_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                            . " WHERE id = '$client_id' ") or die(mysqli_error($db->connection));
        }else{
            $lb = basicValidation($_POST["pib"]);
            $db->connection->query("UPDATE client "
                            . " SET type_id='$type_id', name='$name', name_note='$name_note', lb='$lb', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', address_note='$address_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                            . " WHERE id = '$client_id' ") or die(mysqli_error($db->connection));
        }
    }

    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}

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
