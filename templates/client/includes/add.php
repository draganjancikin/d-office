<?php
// add client
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["newClient"])) {

    $user_id = $_SESSION['user_id'];
    $date = date('Y-m-d h:i:s');

    $vps_id = $_POST["vps_id"];

    if (empty($_POST['name'])) {
        $nameError = 'Ime mora biti upisano';
        die('<script>location.href = "?new&name_error" </script>');
	} else {
		$name = basicValidation($_POST['name']);
    }

    $name_note = basicValidation($_POST["name_note"]);
    if(isset($_POST["is_supplier"])) {
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

    $db = new DatabaseController();
    
    // citamo iz baze, iz tabele client sve zapise gde je name=$name
    $provera_upit = $db->connection->query( "SELECT name FROM client WHERE name='$name'");

    // count number of rows
    $broj_naziva = mysqli_num_rows($provera_upit);

    if ($broj_naziva>0){
        die('<script>location.href = "?alert&ob=2" </script>');
    }else{

        if ($vps_id == 1){
            //MySqli Insert Query
            $insert_row = $db->connection->query( " INSERT INTO client (vps_id, name, name_note, is_supplier, state_id, city_id, street_id, home_number, address_note, note, created_at_date, created_at_user_id) "
                                            . " VALUES ('$vps_id', '$name', '$name_note', '$is_supplier', '$state_id', '$city_id', '$street_id', '$home_number', '$address_note', '$note', '$date', '$user_id') ") or die(mysqli_error($connect_db));
        } elseif ($vps_id == 2){
            $lb = basicValidation($_POST["pib"]);
            //MySqli Insert Query
            $insert_row = $db->connection->query( " INSERT INTO client (vps_id, name, name_note, lb, is_supplier, state_id, city_id, street_id, home_number, address_note, note, created_at_date, created_at_user_id) "
                                            . " VALUES ('$vps_id', '$name', '$name_note', '$lb', '$is_supplier', '$state_id', '$city_id', '$street_id', '$home_number', '$address_note', '$note', '$date', '$user_id') ") or die(mysqli_error($connect_db));
        }

        if($insert_row){
            $client_id = $db->connection->insert_id;
            die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
        }else{
            die('Error : ('. $db->connection->errno .') '. $db->connection->error);
        }

    }

}

// add client contact
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["newContact"])) {

    $client_id = htmlspecialchars($_POST["client_id"]);
    $type_id = htmlspecialchars($_POST["contacttype_id"]);
    $number = basicValidation($_POST["number"]);
    $note = basicValidation($_POST["note"]);

    $db = new DatabaseController();

    $db->connection->query("INSERT INTO contacts (type_id, number, note) VALUES ('$type_id', '$number', '$note') ") or die(mysqli_error($db->connection));

    $contact_id = $db->connection->insert_id;
    $db->connection->query("INSERT INTO client_contacts (client_id, contact_id) VALUES ('$client_id', '$contact_id') ") or die(mysqli_error($db->connection));

    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}

// add State, City and Street
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET["addSCS"])) {

    $action = basicValidation($_POST["action"]);
    $name = basicValidation($_POST["name"]);

    $db = new DatabaseController();

    // citamo iz baze, iz tabele $action sve zapise gde je name=$name
    $str = 'SELECT * FROM ' . $action . ' WHERE name="' . $name . '" ';
    $provera_upit = $db->connection->query( $str );

    // brojimo koliko ima zapisa
    $broj_naziva = mysqli_num_rows($provera_upit);

    if ($broj_naziva>0){
        die('<script>location.href = "?add' . $action . '&alert&ob=2" </script>'); 
    }else{
        $quer = "INSERT INTO ". $action . " (name) VALUES ('" . $name. "') ";
        $db->connection->query($quer) or die(mysqli_error($connect_db));	
        die('<script>location.href = "/clients/" </script>');	
    }

}
