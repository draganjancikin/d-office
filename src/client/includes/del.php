<?php

// delete contact of client
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["delContact"])) {
    
    $client_id = htmlspecialchars($_GET["client_id"]);
    $contact_id = htmlspecialchars($_GET["contact_id"]);
    $contact->delContact($client_id, $contact_id);
    die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
    
}
