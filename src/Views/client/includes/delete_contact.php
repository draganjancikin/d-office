<?php
// Delete clients contact.
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET) && str_contains($_GET['url'], 'removeContact')) {

  $client = $entityManager->find("\App\Entity\Client", $client_id);
  $contact = $entityManager->find("\App\Entity\Contact", $contact_id);

  // Remove $contact from table v6_client_contacts.
  $client->getContacts()->removeElement($contact);
    
  $entityManager->remove($contact);
  $entityManager->flush();
  
  die('<script>location.href = "/client/'.$client_id.'" </script>');
}
