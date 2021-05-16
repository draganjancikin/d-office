<?php
// Delete contact of client.
if($_SERVER["REQUEST_METHOD"] == "GET" AND isset($_GET["deleteContact"])) {
  
  $client_id = htmlspecialchars($_GET["client_id"]);
  $client = $entityManager->find("\Roloffice\Entity\Client", $client_id);

  $contact_id = htmlspecialchars($_GET["contact_id"]);
  $contact = $entityManager->find("\Roloffice\Entity\Contact", $contact_id);

  // Remove $contact from table v6_client_contacts.
  $client->getContacts()->removeElement($contact);
    
  $entityManager->remove($contact);
  $entityManager->flush();
  
  die('<script>location.href = "?view&client_id='.$client_id.'" </script>');
}
