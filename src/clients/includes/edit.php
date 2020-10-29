<?php

// client edit
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editClient"])) {

    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');

    $client_id = $_POST["client_id"];
    $vps_id = $_POST["vps_id"];
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

    $db = new DB();
    $connection = $db->connectDB();

    if ($vps_id == 1){
        $connection->query(" UPDATE client " 
                         . " SET vps_id='$vps_id', name='$name', name_note='$name_note', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', address_note='$address_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                         . " WHERE id = '$client_id' ") or die(mysqli_error($connection));
    }

    if ($vps_id == 2){
        if ( $vps_id == 2 AND !isset($_POST["pib"]) ) {
            $connection->query("UPDATE client "
                            . " SET vps_id='$vps_id', name='$name', name_note='$name_note', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', address_note='$address_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                            . " WHERE id = '$client_id' ") or die(mysqli_error($connection));
        }else{
            $lb = basicValidation($_POST["pib"]);
            $connection->query("UPDATE client "
                            . " SET vps_id='$vps_id', name='$name', name_note='$name_note', lb='$lb', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', address_note='$address_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                            . " WHERE id = '$client_id' ") or die(mysqli_error($connection));
        }
    }

    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}

// contact of client edit
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editContact"])) {

    $contact_id = $_POST["id"];
    $client_id = basicValidation($_GET["client_id"]);
    $contacttype_id = $_POST["contacttype_id"];
    $number = basicValidation($_POST["number"]);
    $note = basicValidation($_POST["note"]);

    $db = new DB();
    $connection = $db->connectDB();

    $connection->query("UPDATE contacts "
                    . " SET type_id='$contacttype_id', number='$number', note='$note' "
                    . " WHERE id = '$contact_id' ") or die(mysqli_error($connection));

    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}
