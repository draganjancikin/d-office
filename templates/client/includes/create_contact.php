<?php
// Create new contact.
if($_SERVER["REQUEST_METHOD"] == "POST" AND isset($_GET["createContact"])) {

  // Curent loged user.
  $user_id = $_SESSION['user_id'];
  $user = $entityManager->find("\Roloffice\Entity\User", $user_id);

  $client_id = $_POST["client_id"];
  $client = $entityManager->find("\Roloffice\Entity\Client", $client_id);

  $type_id = $_POST["contact_type_id"];
  $type = $entityManager->find("\Roloffice\Entity\ClientType", $type_id);

  $contact_type_id = $_POST["contact_type_id"];
  $contact_type = $entityManager->find("\Roloffice\Entity\ContactType", $contact_type_id);
  $body = basicValidation($_POST["body"]);
  $note = basicValidation($_POST["note"]);

  $newContact = new \Roloffice\Entity\Contact();

  $newContact->setType($contact_type);
  $newContact->setBody($body);
  $newContact->setNote($note);
  $newContact->setCreatedAt(new DateTime("now"));
  $newContact->setCreatedByUser($user);
  $newContact->setModifiedAt(new DateTime("0000-01-01 00:00:00"));

  $entityManager->persist($newContact);
  $entityManager->flush();

  // Add $newContact to table v6_client_contacts_test.
  $client->getContacts()->add($newContact);
  
  $entityManager->flush();

  die('<script>location.href = "?viewClient&client_id='.$client_id.'" </script>');
}
