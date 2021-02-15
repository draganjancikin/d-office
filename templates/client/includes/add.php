<?php
use Roloffice\Core\Database;

// add State, City and Street
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET["addSCS"])) {

    $action = basicValidation($_POST["action"]);
    $name = basicValidation($_POST["name"]);

    $db = new Database();

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
