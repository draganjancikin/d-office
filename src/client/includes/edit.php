<?php

// client edit
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editClient"])) {
    
    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');
    
    $client_id = htmlspecialchars($_POST["client_id"]);
    $vps_id = htmlspecialchars($_POST["vps_id"]);
    $name = htmlspecialchars($_POST["name"]);
    $name_note = htmlspecialchars($_POST["name_note"]);
    
    if ( isset($_POST["is_supplier"]) ) {
        $is_supplier = htmlspecialchars($_POST["is_supplier"]);
    } else {
        $is_supplier = 0;
    }

    $state_id = htmlspecialchars($_POST["state_id"]);
    $city_id = htmlspecialchars($_POST["city_id"]);
    $street_id = htmlspecialchars($_POST["street_id"]);
    $home_number = htmlspecialchars($_POST["home_number"]);
    $adress_note = htmlspecialchars($_POST["adress_note"]);
    $note = htmlspecialchars($_POST["note"]);
    
    $db = new DB();
    $connection = $db->connectDB();
    
    if ($vps_id == 1){
        $connection->query(" UPDATE client " 
                         . " SET vps_id='$vps_id', name='$name', name_note='$name_note', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', adress_note='$adress_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                         . " WHERE id = '$client_id' ") or die(mysqli_error($connection));
    }

    if ($vps_id == 2){
        if ( $vps_id == 2 AND !isset($_POST["pib"]) ) {
            $connection->query("UPDATE client "
                            . " SET vps_id='$vps_id', name='$name', name_note='$name_note', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', adress_note='$adress_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                            . " WHERE id = '$client_id' ") or die(mysqli_error($connection));
        }else{
            $lb = htmlspecialchars($_POST["pib"]);
            $connection->query("UPDATE client "
                            . " SET vps_id='$vps_id', name='$name', name_note='$name_note', lb='$lb', is_supplier='$is_supplier', state_id='$state_id', city_id='$city_id', street_id='$street_id', home_number='$home_number', adress_note='$adress_note', note='$note', modified_at_date='$date', modified_at_user_id='$user_id' "
                            . " WHERE id = '$client_id' ") or die(mysqli_error($connection));
        }
    }
    
    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}

// contact of client edit
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["editContact"])) {
    
    $contact_id = htmlspecialchars($_POST["id"]);
    $client_id = htmlspecialchars($_GET["client_id"]);
    $contacttype_id = htmlspecialchars($_POST["contacttype_id"]);
    $number = htmlspecialchars($_POST["number"]);
    $note = htmlspecialchars($_POST["note"]);
    
    $db = new DB();
    $connection = $db->connectDB();
    
    $connection->query("UPDATE contacts "
                    . " SET type_id='$contacttype_id', number='$number', note='$note' "
                    . " WHERE id = '$contact_id' ") or die(mysqli_error($connection));
    
    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}
